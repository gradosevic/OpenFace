# OpenFace
Face recognition solution that can be used as a REST web service

This is a wrapper for [cmusatyalab/openface](https://github.com/cmusatyalab/openface) Docker solution. It contains scripts for automatic environment setup and startup.

# Requirements
- Docker installed on Windows, Mac or Linux

# Installation 

## Windows
- Open start.bat script
- Change "c:/face" to a folder where these files are unpacked
- Run start.bat as administrator
- Follow the instructions and resolve potential issues

## Linux or Mac
- Open "config.conf"
- Change share folder to match share folder on your host machine where these files are unpacked
- Run start.sh script
- Follow the instructions and resolve potential issues

# How it works
The "start" scripts downloads the Docker image and starts the new container if it's not already started. Once the Docker container has started, it mounts this folder on host machine to /app folder inside the container. Scripts should run the web server and you should be able to access it on http://localhost:8080. If needed, you can change the port in these scripts to something else.

# How to use
- Run start.bat (Windows) or start.sh (Mac, Linux)
- Open http://localhost:8080 on your host machine
- 

# More information
---

+ Website: http://cmusatyalab.github.io/openface/
+ [API Documentation](http://openface-api.readthedocs.org/en/latest/index.html)

---

# Licensing
Unless otherwise stated, the source code and trained Torch and Python
model files are copyright Carnegie Mellon University and licensed
under the [Apache 2.0 License](./LICENSE).
Portions from the following third party sources have
been modified and are included in this repository.
These portions are noted in the source files and are
copyright their respective authors with
the licenses listed.

Project | Modified | License
---|---|---|
[Atcold/torch-TripletEmbedding](https://github.com/Atcold/torch-TripletEmbedding) | No | MIT
[facebook/fbnn](https://github.com/facebook/fbnn) | Yes | BSD
