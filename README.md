# Pokémon Event Locator / Subscription Tool

This tool is a simple frontend around the official Pokemon.com event locator.
It's designed to allow you to quickly search for events by country, state, event
type or game type, and then subscribe to those events via any tool that supports
iCal subscriptions.

## How to use this tool

If you just want to use the tool as is, there is a current version hosted on my
server at <https://pokecal.codearoundcorners.com/>. You're free to subscribe to
calendars generated from that site.

If you want to host this yourself, then follow these steps.

1. Clone the code here on GitHub to your own server.
2. Register for an account on timezonedb.com to get an API key
3. Set up a database and run the latest version of the schema file in the
   resources/sql directory to create the base tables used by the tool.
4. (Optional) Set up a read only user and a read/write user.
5. Copy the config.php.sample file in the resources/php directory to config.php
   and set up the necessary values, including your API key
6. (Optional) Set up a MapBox account to allow maps to be displayed
5. Set the update.php script to run via a scheduled job at whatever update
   interval you feel appropriate (I use hourly, there are currently ~2600 events
   on the official site, and at the default of 500 tournaments per run this will
   allow you to populate your database within 6 hours).

You should now have a full copy of the event database that you can use yourself.