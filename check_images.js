
const http = require('http');

http.get('http://localhost:8000', (res) => {
  let html = '';
  res.on('data', (chunk) => {
    html += chunk;
  });
  res.on('end', () => {
    const imgTags = html.match(/<img [^>]*src="[^"]*"/g) || [];
    console.log('Image sources found:');
    imgTags.forEach((tag) => {
      console.log(tag.match(/src="([^"]*)"/)[1]);
    });
  });
}).on('error', (err) => {
  console.error('Error fetching page:', err.message);
});
