@extends('layouts.error')

@section('title', 'Server Error')

@push('style')
<style>
  .error-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 70vh;
    text-align: center;
    padding: 2rem;
  }
  
  .error-icon {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, #422AFB 0%, #7551FF 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2rem;
    animation: bounce 2s infinite;
  }
  
  @keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
      transform: translateY(0);
    }
    40% {
      transform: translateY(-10px);
    }
    60% {
      transform: translateY(-5px);
    }
  }
  
  .error-code {
    font-size: 3rem;
    font-weight: 700;
    color: #422AFB;
    margin-bottom: 1rem;
  }
  
  .error-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1B254B;
    margin-bottom: 1rem;
  }
  
  .error-message {
    font-size: 1rem;
    color: #707EAE;
    margin-bottom: 2rem;
    max-width: 500px;
    line-height: 1.6;
  }
  
  .error-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    justify-content: center;
  }
  
  .btn-primary {
    background: linear-gradient(135deg, #422AFB 0%, #7551FF 100%);
    color: white;
    padding: 0.75rem 2rem;
    border-radius: 0.75rem;
    text-decoration: none;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(66, 42, 251, 0.3);
    color: white;
    text-decoration: none;
  }
  
  .btn-secondary {
    background: #F4F7FE;
    color: #422AFB;
    padding: 0.75rem 2rem;
    border-radius: 0.75rem;
    text-decoration: none;
    font-weight: 500;
    border: 1px solid #E9ECEF;
    cursor: pointer;
    transition: all 0.2s;
  }
  
  .btn-secondary:hover {
    background: #422AFB;
    color: white;
    text-decoration: none;
  }
</style>
@endpush

@section('main')
<div class="error-container">
  <div class="error-icon">
    <i class="fas fa-exclamation-triangle text-white text-5xl"></i>
  </div>
  
  <h1 class="error-code">500</h1>
  
  <h2 class="error-title">Oops! Terjadi Kesalahan Server</h2>
  
  <p class="error-message">
    Maaf, sistem mengalami gangguan sementara. Tim teknis kami sedang bekerja untuk memperbaiki masalah ini. 
    Silakan coba lagi dalam beberapa saat atau hubungi administrator jika masalah berlanjut.
  </p>
  
  <div class="error-actions">
    <a href="{{ route('dashboard') }}" class="btn-secondary">
      <i class="fas fa-arrow-left mr-2"></i>
      Kembali
    </a>
    
    <a href="{{ route('dashboard') }}" class="btn-primary">
      <i class="fas fa-home mr-2"></i>
      Dashboard
    </a>
    
    <button onclick="location.reload()" class="btn-secondary">
      <i class="fas fa-redo mr-2"></i>
      Coba Lagi
    </button>
  </div>
  
  @if(config('app.debug') && isset($exception))
  <details class="mt-8 p-4 bg-gray-100 rounded-lg text-left w-full max-w-2xl">
    <summary class="cursor-pointer font-semibold text-red-600 mb-2">
      Detail Error (Debug Mode)
    </summary>
    <div class="text-sm text-gray-700 font-mono whitespace-pre-wrap">{{ $exception->getMessage() }}</div>
    @if(method_exists($exception, 'getFile'))
    <div class="text-xs text-gray-500 mt-2">
      File: {{ $exception->getFile() }}:{{ $exception->getLine() }}
    </div>
    @endif
  </details>
  @endif
</div>
@endsection