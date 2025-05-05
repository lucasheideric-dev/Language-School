FROM node:20-bullseye

WORKDIR /app

COPY package*.json ./

RUN apt-get update && \
    apt-get install -y \
    nano \
    vim \
    less \
    curl \
    git \
    procps \
    iputils-ping \
    net-tools \
    lsb-release \
    bash \
    htop \
    python3 \
    python3-pip && \
    pip3 install --no-cache-dir robotframework && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

RUN npm install

COPY . .

ENV HOST 0.0.0.0

EXPOSE 3000

CMD ["npm", "start"]
