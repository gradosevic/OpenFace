#!/bin/bash
. config.conf
docker run -v ${hostshare}:/app -p 9000:9000 -p 8000:8000 -p 8080:8080 -t -i bamos/openface "./app/scripts/init.sh"