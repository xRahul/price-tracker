# Cryptocurrency price-tracker
Plot cryptocurrency price change and save price data locally (setup cron to periodically get data)

This contains-
- a modularized php api that gets and writes price data from the exchange api.
- Access logs are generated for each api call.
- An html file to plot the price data using plot.ly
- exchange related information is stored in a config, making extensibility easy.
- htaccess has gzip enabled for smaller filesizes.
- folder structure setup for ftp to cpanel sites.

exchanges supported-
- Zebpay: Bitcoin
- Coinome(API Down): Bitcoin, Litecoin, Bitcoin Cash
