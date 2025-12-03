FROM php:8.2-cli

WORKDIR /app

# Install ZIP and SQLite3 extensions in single RUN
RUN apt-get update && apt-get install -y libzip-dev && \
    docker-php-ext-install zip && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Copy everything
COPY . .

# Set correct permissions
RUN chmod +x start.sh

EXPOSE 5000

CMD ["./start.sh"]
