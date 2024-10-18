# Sử dụng Node.js 18 như một base image
FROM node:18

# Thiết lập thư mục làm việc
WORKDIR /app

# Sao chép package.json và package-lock.json vào container
COPY package*.json ./

# Cài đặt dependencies
RUN npm install

# Sao chép mã nguồn vào container
COPY . .

# Build ứng dụng
RUN npm run build

# Expose port 3000
EXPOSE 3000

# Khởi động ứng dụng
CMD ["npm", "start"]
