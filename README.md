# OpenFace
Docker based face recognition solution that can be used as a REST web service

*Free and open source face recognition with
deep neural networks.*

---

+ Website: http://cmusatyalab.github.io/openface/
+ [API Documentation](http://openface-api.readthedocs.org/en/latest/index.html)

---

This research was supported by the National Science Foundation (NSF)
under grant number CNS-1518865.  Additional support
was provided by the Intel Corporation, Google, Vodafone, NVIDIA, and the
Conklin Kistler family fund.  Any opinions, findings, conclusions or
recommendations expressed in this material are those of the authors
and should not be attributed to their employers or funding sources.

Please cite OpenFace in your publications if it helps your research.
The following is a [BibTeX](http://www.bibtex.org/) and plaintext reference for our
[OpenFace tech report](http://reports-archive.adm.cs.cmu.edu/anon/anon/2016/CMU-CS-16-118.pdf).

```
@techreport{amos2016openface,
  title={OpenFace: A general-purpose face recognition
    library with mobile applications},
  author={Amos, Brandon and Bartosz Ludwiczuk and Satyanarayanan, Mahadev},
  year={2016},
  institution={CMU-CS-16-118, CMU School of Computer Science},
}

B. Amos, B. Ludwiczuk, M. Satyanarayanan,
"Openface: A general-purpose face recognition library with mobile applications,"
CMU-CS-16-118, CMU School of Computer Science, Tech. Rep., 2016.
```

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
