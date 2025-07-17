function randomInt(max) {
  return Math.floor(Math.random() * max);
}

function updateMetrics() {
  document.getElementById('calls').textContent = randomInt(50);
  document.getElementById('wait').textContent = randomInt(300);
  document.getElementById('agents-online').textContent = randomInt(25);

  const queue = document.getElementById('queue');
  queue.innerHTML = '';
  for (let i = 0; i < 5; i++) {
    const tr = document.createElement('tr');
    const priority = ['Low', 'Medium', 'High'][randomInt(3)];
    tr.innerHTML = `<td>Caller ${i + 1}</td><td>Inquiry</td><td>${priority}</td>`;
    queue.appendChild(tr);
  }
}

setInterval(updateMetrics, 3000);
updateMetrics();
