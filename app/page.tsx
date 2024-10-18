import React from 'react';
import fs from 'fs';
import path from 'path';
import Head from 'next/head';

const HomePage = () => {
  const htmlPath = path.join(process.cwd(), 'public', 'index.html');
  const htmlContent = fs.readFileSync(htmlPath, 'utf8');

  return (
    <>
      <Head>
        <title>LAZINET - Effective Technologies</title>
        <meta name="description" content="Your description here" />
        <meta name="keywords" content="Your keywords here" />
        <link rel="icon" href="/assets/img/lazinet_LogoOnly.png" />
      </Head>
      <div dangerouslySetInnerHTML={{ __html: htmlContent }} />
    </>
  );
};

export default HomePage;



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
