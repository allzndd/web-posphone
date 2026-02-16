@extends('layouts.app')

@section('title', 'Chat Analisis')

@push('style')
<style>
  .chat-container { 
    display: flex; 
    flex-direction: column; 
    height: calc(100vh - 100px); 
    overflow: hidden; 
  }
  .chat-suggestions { 
    padding: 12px 20px; 
    background: #ffffff; 
    border-top: 1px solid #E9ECEF; 
    flex-shrink: 0; 
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
  }
  .chat-suggestions .btn { 
    font-size: 0.8rem;
    white-space: nowrap;
  }
  .btn-brand-outline {
    background: transparent;
    color: #422AFB;
    border: 1px solid rgba(66, 42, 251, 0.3);
    border-radius: 0.75rem;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.2s;
  }
  .btn-brand-outline:hover {
    background: #422AFB;
    color: #ffffff;
    border-color: #422AFB;
  }
  .chat-log { 
    flex: 1; 
    overflow-y: auto; 
    padding: 20px; 
    background: #ffffff; 
    min-height: 0; 
  }
  .chat-bubble { 
    max-width: 70%; 
    padding: 12px 16px; 
    border-radius: 16px; 
    line-height: 1.5; 
    word-wrap: break-word; 
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
  }
  .chat-me { 
    background: #422AFB; 
    color: #ffffff; 
    border-bottom-right-radius: 4px; 
    font-weight: 500; 
  }
  .chat-bot { 
    background: #F4F7FE; 
    color: #1B254B; 
    border-bottom-left-radius: 4px; 
    border: 1px solid #E9ECEF; 
  }
  .chat-row { 
    display: flex; 
    gap: 12px; 
    margin-bottom: 16px; 
    align-items: flex-end;
  }
  .chat-row.me { 
    justify-content: flex-end; 
  }
  .chat-row.bot { 
    justify-content: flex-start; 
  }
  .chat-avatar { 
    width: 40px; 
    height: 40px; 
    border-radius: 50%; 
    display: flex; 
    align-items: center; 
    justify-content: center; 
    flex-shrink: 0; 
    font-size: 18px;
  }
  .chat-avatar.me { 
    background: linear-gradient(135deg, #422AFB 0%, #7551FF 100%); 
    color: #ffffff; 
  }
  .chat-avatar.bot { 
    background: #F4F7FE; 
    color: #422AFB; 
    border: 2px solid #E9ECEF;
  }
  .chat-time { 
    font-size: 11px; 
    opacity: 0.6; 
    margin-top: 6px; 
  }
  .chat-answer table { 
    margin-top: 12px; 
    border-radius: 8px;
    overflow: hidden;
  }
  .chat-answer {
    margin-top: 12px;
    border-radius: 8px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    display: block;
    max-width: 100%;
  }
  .chat-answer table {
    margin: 0;
    min-width: 100%;
    border-collapse: collapse;
  }
  .chat-answer table thead {
    background: #422AFB;
    color: #ffffff;
  }
  .chat-answer table th {
    padding: 8px 12px;
    font-weight: 600;
    font-size: 0.875rem;
  }
  .chat-answer table td {
    padding: 8px 12px;
    background: #ffffff;
    border-bottom: 1px solid #E9ECEF;
    font-size: 0.875rem;
  }
  .chat-input-bar { 
    padding: 16px 20px; 
    background: #ffffff; 
    border-top: 1px solid #E9ECEF; 
    flex-shrink: 0; 
  }
  .btn-send-spinner { 
    display: none; 
  }
  .sending .btn-send-text { 
    display: none; 
  }
  .sending .btn-send-spinner { 
    display: inline-block; 
  }
  #chat-log .chat-row:first-child {
    margin-top: 60px;
  }
</style>
@endpush

@section('main')
<style>
  /* Hide footer for chat page only */
  body:has([data-page="chat"]) footer,
  body:has([data-page="chat"]) .p-3:has(footer) {
    display: none !important;
  }
</style>
@include('components.access-denied-overlay', ['module' => 'Chat Analisis', 'hasAccessRead' => $hasAccessRead])
<div class="h-full" data-page="chat" @if(!$hasAccessRead) style="opacity: 0.3; pointer-events: none;" @endif>
    <div class="bg-white h-full overflow-hidden">
        <div class="chat-container">
            <div id="chat-log" class="chat-log"></div>
            <div class="chat-suggestions">
                <button type="button" class="btn btn-brand-outline sugg">Produk terlaris</button>
                <button type="button" class="btn btn-brand-outline sugg">Toko yang paling banyak penjualan</button>
                <button type="button" class="btn btn-brand-outline sugg">Total penjualan hari ini</button>
                <button type="button" class="btn btn-brand-outline sugg">Cek stok produk</button>
            </div>
            <div class="chat-input-bar">
                @php
                    $hasAccessAsk = \App\Services\PermissionService::check('chat.ask') || \App\Services\PermissionService::check('chat.create');
                @endphp
                <form id="chat-form">
                    <div class="flex gap-2">
                        <input id="chat-input" type="text" 
                               class="flex-1 rounded-xl border border-gray-200 px-4 py-3 text-sm font-medium text-navy-700 outline-none focus:border-brand-500" 
                               placeholder="Ketik pertanyaan Anda…" 
                               {{ !$hasAccessAsk ? 'disabled' : '' }}
                               required 
                               maxlength="500">
                        <button class="px-6 py-3 bg-brand-500 text-white rounded-xl font-medium hover:bg-brand-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                                type="submit"
                                {{ !$hasAccessAsk ? 'disabled' : '' }}>
                            <span class="btn-send-text"><i class="fas fa-paper-plane mr-2"></i>Kirim</span>
                            <span class="btn-send-spinner"><i class="fas fa-spinner fa-spin mr-2"></i>Mengirim…</span>
                        </button>
                    </div>
                    @if(!$hasAccessAsk)
                    <p class="text-sm text-gray-500 mt-2 italic">✓ Anda memiliki akses membaca chat, tapi tidak memiliki permission untuk mengirim pertanyaan. Hubungi admin untuk enable permission chat.create atau chat.ask.</p>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function(){
  const form = document.getElementById('chat-form');
  const input = document.getElementById('chat-input');
  const log = document.getElementById('chat-log');
  const suggestions = document.querySelectorAll('.sugg');

  function el(tag, className, html){
    const e = document.createElement(tag);
    if(className) e.className = className;
    if(html !== undefined) e.innerHTML = html;
    return e;
  }

  function escapeHtml(str){
    return str.replace(/[&<>\"]/g, function(c){
      return {'&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;'}[c] || c;
    });
  }

  function formatText(text){
    // preserve line breaks
    return escapeHtml(text).replace(/\n/g, '<br>');
  }

  function appendTable(data){
    if(!Array.isArray(data) || !data.length || typeof data[0] !== 'object') return null;
    const cols = Object.keys(data[0]);
    const table = el('table', 'table table-sm table-bordered mb-0');
    const thead = el('thead');
    const trh = el('tr');
    cols.forEach(k=> trh.appendChild(el('th', '', escapeHtml(k))));
    thead.appendChild(trh);
    table.appendChild(thead);
    const tbody = el('tbody');
    data.forEach(row => {
      const tr = el('tr');
      cols.forEach(k => tr.appendChild(el('td', '', escapeHtml(String(row[k] ?? '')))));
      tbody.appendChild(tr);
    });
    table.appendChild(tbody);
    return table;
  }

  function nowTime(){
    try{
      return new Date().toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'});
    }catch(e){ return ''; }
  }

  function appendBubble(text, who, opts={}){
    const row = el('div', 'chat-row ' + (who === 'me' ? 'me' : 'bot'));
    const avatar = el('div', 'chat-avatar ' + (who === 'me' ? 'me' : 'bot'), '<i class="fas ' + (who === 'me' ? 'fa-user' : 'fa-robot') + '"></i>');
    const bubble = el('div', 'chat-bubble ' + (who === 'me' ? 'chat-me' : 'chat-bot'));
    const textEl = el('div', 'chat-text');
    textEl.innerHTML = formatText(text || '');
    bubble.appendChild(textEl);
    if (opts.data) {
      const table = appendTable(opts.data);
      if (table) {
        const answerWrap = el('div', 'chat-answer');
        answerWrap.appendChild(table);
        bubble.appendChild(answerWrap);
      }
    }
    const timeEl = el('div', 'chat-time');
    timeEl.textContent = nowTime();
    bubble.appendChild(timeEl);
    if(who === 'me'){
      row.appendChild(bubble);
      row.appendChild(avatar);
    } else {
      row.appendChild(avatar);
      row.appendChild(bubble);
    }
    log.appendChild(row);
    log.scrollTop = log.scrollHeight;
  }

  function setSending(sending){
    const btn = form.querySelector('button[type="submit"]');
    if(sending){
      form.classList.add('sending');
      btn.disabled = true; input.disabled = true;
    } else {
      form.classList.remove('sending');
      btn.disabled = false; input.disabled = false; input.focus();
    }
  }

  form.addEventListener('submit', async function(e){
    e.preventDefault();
    
    // Check if user has permission to ask (form will be disabled if no permission)
    if (input.disabled) {
      alert('Anda tidak memiliki permission untuk mengirim pertanyaan. Hubungi admin untuk enable permission chat.create atau chat.ask.');
      return;
    }
    
    const message = input.value.trim();
    if(!message) return;
    appendBubble(message, 'me');
    input.value = '';
    setSending(true);
    try{
      const response = await fetch('{{ route('chat.ask') }}', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json'
        },
        body: JSON.stringify({ message })
      });
      
      if (!response.ok) {
        appendBubble('Gagal memproses (status ' + response.status + ').', 'bot');
        return;
      }
      
      const json = await response.json();
      if (json.ok) {
        appendBubble(json.answer || 'Tidak ada respons.', 'bot', { data: json.data });
      } else {
        appendBubble(json.error || 'Terjadi kesalahan.', 'bot');
      }
    }catch(err){
      appendBubble('Gagal terhubung ke server.', 'bot');
    } finally {
      setSending(false);
    }
  });

  suggestions.forEach(btn => btn.addEventListener('click', function(){
    input.value = this.textContent.trim();
    form.dispatchEvent(new Event('submit'));
  }));

  // Enter to send, Shift+Enter for newline
  input.addEventListener('keydown', function(e){
    if(e.key === 'Enter' && !e.shiftKey){
      e.preventDefault();
      form.dispatchEvent(new Event('submit'));
    }
  });

  // Initial welcome message from bot
  function showWelcome(){
    const welcome = [
      'Halo! Saya asisten AI PosPhone yang siap membantu Anda. 🤖',
      '',
      'Saya bisa membantu Anda untuk:',
      '- Cek produk terlaris',
      '- Lihat penjualan hari ini',
      '- Cek stok produk',
      '- Mencari produk',
      '- Cek harga produk',
      '- Toko dengan penjualan terbanyak',
      '- Produk di toko tertentu',
      '',
      'Silahkan tanyakan apa saja! 😊'
    ].join('\n');
    appendBubble(welcome, 'bot');
  }

  showWelcome();
});
</script>
@endpush
