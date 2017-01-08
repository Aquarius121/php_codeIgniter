#!/bin/bash

mv mail.log.5 mail.log.6
mv mail.log.4 mail.log.5
mv mail.log.3 mail.log.4
mv mail.log.2 mail.log.3
mv mail.log.1 mail.log.2
mv mail.log   mail.log.1

> mail.log
