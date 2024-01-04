<?php

namespace App\Controller;

use Psr\Cache\CacheItemPoolInterface;
use SpotifyWebAPI\Session;
use SpotifyWebAPI\SpotifyWebAPI;
use SpotifyWebAPI\SpotifyWebAPIAuthException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SpotifyController extends AbstractController
{
    public function __construct(
        private readonly SpotifyWebAPI $api,
        private readonly Session $session,
        private readonly CacheItemPoolInterface $cache,
    ) {
    }

    #[Route('/', name: 'app_spotify_update_my_playlist')]
    public function updateMyPlaylist(): Response
    {
        // If we don't have an access token in the cache, redirect to Spotify
        if (!$this->cache->hasItem('spotify_access_token')) {
            return $this->redirectToRoute('app_spotify_redirect');
        }

        // Set the access token on the API wrapper
        $this->api->setAccessToken($this->cache->getItem('spotify_access_token')->get());
        
        // Get my top 20 tracks from the last 4 weeks
        $top30 = $this->api->getMyTop('tracks', [
            'limit' => 30,
            'time_range' => 'short_term',
        ]);

        // Get the IDs of the tracks
        $top30TrackIds = array_map(function ($track) {
            return $track->id;
        }, $top30->items);

        // Get Playlist ID from Symfony Secrets
        $playlistId = $this->getParameter('SPOTIFY_PLAYLIST_ID');

        // Replace the tracks in the playlist with the top 20 tracks
        $this->api->replacePlaylistTracks($playlistId, $top30TrackIds);

        return $this->render('spotify/index.html.twig', [
            'tracks' => $this->api->getPlaylistTracks($playlistId),
        ]);
    }

    #[Route('/callback', name: 'app_spotify_callback')]
    public function callbackFromSpotify(Request $request): Response
    {
        // Request an access token using the code from Spotify
        try {
            $this->session->requestAccessToken($request->query->get('code'));
        } catch (SpotifyWebAPIAuthException $e) {
            return new Response($e->getMessage(), 400, ['Content-Type' => 'text/plain']);
        }

        // Save the access token and refresh token to the cache
        $cacheItem = $this->cache->getItem('spotify_access_token');
        $cacheItem->set($this->session->getAccessToken());
        $cacheItem->expiresAfter(3600);
        $this->cache->save($cacheItem);

        return $this->redirectToRoute('app_spotify_update_my_playlist');
    }

    #[Route('/redirect', name: 'app_spotify_redirect')]
    public function redirectToSpotify(): Response
    {
        // Scope defines what permissions that we need the user to grant
        $options = [
            'scope' => [
                'user-read-email',
                'user-read-private',
                'playlist-read-private',
                'playlist-modify-private',
                'playlist-modify-public',
                'user-top-read',
            ],
        ];

        // Redirect the user to Spotify to authorize us
        return $this->redirect($this->session->getAuthorizeUrl($options));
    }
}
