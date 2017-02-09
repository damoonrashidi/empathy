# Ubuntu Dockerfile
# https://github.com/dockerfile/ubuntu

# Pull base image.
FROM ubuntu:latest

# Add files.
COPY ./empathy-1.0.2.gem /root/empathy.gem
EXPOSE 80

# Install.
RUN \
  sed -i 's/# \(.*multiverse$\)/\1/g' /etc/apt/sources.list && \
  apt-get update && \
  apt-get -y upgrade && \
  apt-get install -y build-essential software-properties-common curl ruby htop wget mysql-server mysql-client && \
  rm -rf /var/lib/apt/lists/*

RUN \
  wget https://git.io/psysh && \
  chmod +x psysh && \
  gem install /root/empathy.gem