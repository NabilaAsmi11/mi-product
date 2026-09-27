FROM node:20-alpine

WORKDIR /app

COPY package*.json ./
RUN npm install --production

COPY . .

EXPOSE 3001

# Ubah baris ini:
CMD ["node", "app.js"]
