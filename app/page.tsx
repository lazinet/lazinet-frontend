import React from 'react';
import fs from 'fs';
import path from 'path';

const HomePage = () => {
  const htmlPath = path.join(process.cwd(), 'public', 'index.html');
  const htmlContent = fs.readFileSync(htmlPath, 'utf8');

  return (
    <div dangerouslySetInnerHTML={{ __html: htmlContent }} />
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
