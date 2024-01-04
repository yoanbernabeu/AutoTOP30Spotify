
# AutoTOP30Spotify

AutoTOP30Spotify is a Symfony application designed to automatically update a Spotify playlist with the top 30 most listened tracks over the last 30 days.

## Prerequisites
Before you start, make sure you have the following:
- PHP 8.2 or higher
- Composer
- A Spotify account (for API keys)

## Configuration
To configure the application, set the following variables in your `.env` file:
```
SPOTIFY_CLIENT_ID=your_client_id
SPOTIFY_CLIENT_SECRET=your_client_secret
SPOTIFY_REDIRECT_URI=your_redirect_uri
SPOTIFY_PLAYLIST_ID=your_playlist_id
```

*The `SPOTIFY_REDIRECT_URI` is the URL where Spotify will redirect the user after authentication. It must be a valid URL, in this application it's finished with `/callback` (e.g. `https://127.0.0.1:8000/callback`).*

## Installation
1. Clone the AutoTOP30Spotify GitHub repository:
   ```
   git clone git@github.com:yoanbernabeu/AutoTOP30Spotify.git
   ```
2. Install dependencies using Composer:
   ```
   composer install
   ```

## Usage
To use the application, start your Symfony server with the following command:
```
symfony serve
```
Then open your browser and navigate to the root URL `/` (e.g. `https://127.0.0.1:8000/`).

Follow the instructions to accept access requests related to your Spotify account.

## Educational Project
This project is part of an educational initiative for the [YoanDevCo YouTube channel](https://www.youtube.com/@yoandevco). It's designed to provide practical examples and tutorials on Symfony and API integration.
