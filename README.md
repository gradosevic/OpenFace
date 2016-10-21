# OpenFace
Face recognition solution that can be used as a REST web service. Send requests to a web service to check images and receive a JSON response.

This is a wrapper for [cmusatyalab/openface](https://github.com/cmusatyalab/openface) Docker solution. It contains scripts for automatic environment setup and startup. This project was created in order to start quickly using OpenFace - face recognition features and gives you the flexibility to integrate it with any existing project that can consume the REST service. The setup should hopefully be much easier and automated, comparing to the original project.

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
The "start" scripts downloads the Docker image and starts the new container if it's not already started. Once the Docker container has started, it mounts this folder on host machine to /app folder inside the container. Script should start the web server and you should be able to access it on <b>http://localhost:8080</b>. If needed, you can change the port in these scripts to something else.

# How to use
- Run start.bat (Windows) or start.sh (Mac, Linux)
- Open http://localhost:8080 on your host machine
- Run demo <b>http://localhost:8080/?test=test/&ast;&with=known/&ast;</b>
- You should receive a JSON response after few moments
- Add new folders with images to /app/images/ to test your own images

# Response
Response is returned in this format:

```
Array
(
    [<known_image>] => Array
        (
            [<testing_image>] => <probability>
        )
)
```

For example, if we send this request <b>http://localhost:8080/?test=test/&ast;&with=known/&ast;</b>, a response like this will be returned:
```
{
  "clapton-1.jpg": {
    "clapton-2.jpg": 0.318
  },
  "lennon-1.jpg": {
    "lennon-2.jpg": 0.763
  }
}
```

This is the folders structure for the example:

```
images/
-- known/
---- clapton-1.jpg
---- lennon-1.jpg
-- test/
---- adams.jpg
---- carell.jpg
---- clapton-2.jpg
---- lennon-2.jpg
---- longoria-cooper.jpg
```

It is possible to use the same folder for both parameters. In that case it will test every image with another and return the matching results of comparison.


# Parameters
Use these parameters in your URL
- <b>test</b> - Folder with images that should be tested
 - Required (string). Examples: <b>test/&ast;, test/image1.jpg, test/ima&ast;</b> 
- <b>with</b> - Test images from previous folder with images in this folder (can be the same folder)
 - Required (string). Examples: <b>folder2/&ast;, folder2/image2.jpg, test/image.&ast;</b>
- <b>t</b> - Threshold for matching the images. Can be between 0-4. Images with comparison < 1 are considered as matched
 - Optional (float). Default: 1
- <b>skip-matched</b> - If set, when used multiple images in "with" folder, it will skip already matched images in the next iteration.
 - Optional (boolean). Default: false
- <b>debug</b> - If set, results will be displayed in readable form, instead of JSON
 - Optional (boolean). Default: false
- <b>callback</b> - When set, script will send a POST request to the provided callback URL. Can be useful to send a request and to get notified when it's done
 - Optional (URL). Default: not set (empty string)
 
# Important notes
- Every time "start" script runs, it creates new Docker container. Use "clear" script to clear all previously used Docker containers. You can add this code at the beginning of "start" script to clear up all containers before starting new one.
 

# More information
---

+ Original demo: http://cmusatyalab.github.io/openface/demo-2-comparison/
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
