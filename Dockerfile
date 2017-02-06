#
# Ubuntu Dockerfile
#
# https://github.com/dockerfile/ubuntu
#

# Pull base image.
FROM ubuntu:14.04

# Add files.
COPY ./empathy-1.0.2.gem /root/empathy.gem

# Install.
RUN \
  sed -i 's/# \(.*multiverse$\)/\1/g' /etc/apt/sources.list && \
  apt-get update && \
  apt-get -y upgrade && \
  apt-get install -y build-essential && \
  apt-get install -y software-properties-common && \
  apt-get install -y curl htop ruby wget mysql-server mysql-client && \
  rm -rf /var/lib/apt/lists/*


RUN \
  wget https://git.io/psysh && \
  chmod +x psysh
  # gem install /root/empathy.gem