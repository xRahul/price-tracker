# Bitcoin price-tracker
Plot Bitcoin Price change and save price data locally (setup cron to periodically get data)

This repository contains-
- a php api that gets and writes bitcoin price data from the exchange api.
- an html file to plot the price data using plotly.
- you can set a minimum time before making the next exchange api call (default is 2 mins).
- htaccess has gzip enabled for smaller filesizes.
- folder structure setup for ftp to cpanel sites.

exchanges supported-
- Zebpay
- Coinome