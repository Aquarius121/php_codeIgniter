#!/bin/bash

mv request.log.5 request.log.6
mv request.log.4 request.log.5
mv request.log.3 request.log.4
mv request.log.2 request.log.3
mv request.log.1 request.log.2
mv request.log   request.log.1

> request.log
