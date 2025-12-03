#!/bin/bash
PORT=${PORT:-5000}
php -S 0.0.0.0:$PORT -t public
