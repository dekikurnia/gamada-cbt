FROM node:22-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install
RUN npm install axios

# copy seluruh project (supaya bisa dijalankan)
COPY . .

EXPOSE 5173

ENV FORCE_COLOR=1

# Jalankan dalam mode dev dan biarkan host bisa akses
CMD ["npm", "run", "dev", "--", "--host", "0.0.0.0"]
