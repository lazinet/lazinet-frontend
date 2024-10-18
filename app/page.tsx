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

// Chat GPT hướng dẫn đoạn thêm icon vào public không ổn
// import React from 'react';
// import fs from 'fs';
// import path from 'path';
// import Head from 'next/head';

// const HomePage = () => {
//   const htmlPath = path.join(process.cwd(), 'public', 'index.html');
//   const htmlContent = fs.readFileSync(htmlPath, 'utf8');

//   return (
//     <>
//       {/* Chỉnh title trong layout.tsx và copy ảnh logo với tên icon.ico vào thư mục app */}
//       <Head>
//         {/* Thêm title */}
//         <title>LAZINET - Effective Technologies</title>
//         {/* Thêm favicon, nằm trong /public/icon.ico */}
//         {/* <link rel="icon" href="/icon.ico" /> */}
//       </Head>
//       <div dangerouslySetInnerHTML={{ __html: htmlContent }} />
//     </>
//   );
// };

// export default HomePage;



// import React from 'react';

// const HomePage = () => {
//   return (
//     <div>
//       <head>
//         <link rel="stylesheet" href="/css/styles.css" />
//         <title>Lazinet Company</title>
//       </head>
//       <header>
//         <h1>Welcome to Lazinet</h1>
//       </header>
//       <main>
//         <p>This is the company introduction page.</p>
//       </main>
//       <footer>
//         <p>© 2024 Lazinet</p>
//       </footer>
//       <script src="/js/scripts.js"></script>
//     </div>
//   );
// };

// export default HomePage;

// import React from 'react';
// import Head from 'next/head';

// const HomePage = () => {
//   return (
//     <>
//       <Head>
//         <title>LAZINET - Effective Technologies</title>
//         <meta name="description" content="LAZINET - The best tech solutions" />
//         <meta name="keywords" content="tech, solutions, effective" />
//         <link rel="icon" href="/assets/img/lazinet_LogoOnly.png" />
//       </Head>
//       <h1>Welcome to LAZINET</h1>
//     </>
//   );
// };

// export default HomePage;

// /src/app/layout.tsx

// export const metadata = {
//   // Các thuộc tính metadata khác của bạn
//   title: 'LAZINET - Effective Technologies', // Thay thế title cho trang của bạn
//   description: 'Your website description', // Mô tả trang
//   icons: {
//     icon: [
//       {
//         url: "/favicon.ico", // Đường dẫn đến icon trong /public
//         href: "/favicon.ico", // Đường dẫn đến icon trong /public
//       },
//     ],
//   },
// };

// export default function RootLayout({
//   children,
// }: {
//   children: React.ReactNode;
// }) {
//   return (
//     <html lang="en">
//       <body>{children}</body>
//     </html>
//   );
// }
