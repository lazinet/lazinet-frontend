import React from 'react';
import fs from 'fs';
import path from 'path';
// import Head from 'next/head';

const HomePage = () => {
  const htmlPath = path.join(process.cwd(), 'public', 'index.html');
  const htmlContent = fs.readFileSync(htmlPath, 'utf8');

  return (
    <>
      {/* Chỉnh title trong layout.tsx và copy ảnh logo với tên icon.ico vào thư mục app */}
      <div dangerouslySetInnerHTML={{ __html: htmlContent }} />
    </>
  );
};
export default HomePage;

