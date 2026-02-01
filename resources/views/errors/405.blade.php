@extends('layouts.error')

@section('title', 'Method Not Allowed')

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
    background: linear-gradient(135deg, #F59E0B 0%, #F97316 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2rem;
    animation: wobble 1s ease-in-out infinite;
  }
  
  @keyframes wobble {
    0%, 100% { transform: rotate(0deg); }
    15% { transform: rotate(-5deg); }
    30% { transform: rotate(5deg); }
    45% { transform: rotate(-3deg); }
    60% { transform: rotate(3deg); }
    75% { transform: rotate(-1deg); }
  }
  
  .error-code {
    font-size: 3rem;
    font-weight: 700;
    color: #F59E0B;
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
    <i class="fas fa-ban text-white text-5xl"></i>
  </div>
  
  <h1 class="error-code">405</h1>
  
  <h2 class="error-title">Method Not Allowed</h2>
  
  <p class="error-message">
    Maaf, method HTTP yang Anda gunakan tidak diizinkan untuk halaman ini. 
    Silakan periksa kembali atau hubungi administrator.
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
  </div>
</div>
@endsection