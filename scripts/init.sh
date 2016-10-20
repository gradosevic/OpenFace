#!/bin/bash
startWebServer(){
    echo "Starting web server..."
    cd app/scripts
    echo "------------------------------------------"
    echo "Open the browser on host machine and navigate ${hostshare} to:"
    echo "http://localhost:8080/"
    echo "------------------------------------------"
    php -S 0.0.0.0:8080
   
}
clear
#ls
cp /app/scripts/compare.py /root/openface/demos/compare.py
echo "--------------------------------------"
echo " Image Comparison App"
echo "--------------------------------------"

if which php >/dev/null; then
    startWebServer
else
    echo "Initializing app..."
    echo "Getting the latest package information..."
    apt-get update
    echo "Installing PHP and server libraries..."
    apt-get install php5-common libapache2-mod-php5 php5-cli
    echo "---------------------------------------------------------------------"
    echo "Libraries are installed. Please do following to preserve Docker changes:"
    echo " - Open new terminal instance on host machine"
    echo " - Execute 'docker ps -l'"
    echo " - Get the Container Instance ID"
    echo " - Execute 'docker commit <ID> bamos/openface'"
    echo " - Note: If you skip these steps, you may need to repeat this process next time"
    echo "------------------------------"
    echo "Press any key to continue...>"
    read input_variable
    #Replace original compare.py file with our file that does not throws exceptions, and can skip issues when they occur
    #It's required to do this only once
    #Check from time to time and compare this file with original from GitHub to see if there are any major changes. 
    #If there are some changes after some time in new version, please update the /app/scripts/compare.py file and add required changes to handle exceptions
    
    #mv /root/openface/demos/compare.py /root/openface/demos/compare-original.py
    #cp /app/scripts/compare.py /root/openface/demos/compare.py
    startWebServer
fi
