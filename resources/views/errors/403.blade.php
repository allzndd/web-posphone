@extends('layouts.error')

@section('title', 'Akses Ditolak')

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
    background: linear-gradient(135deg, #DC2626 0%, #EF4444 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2rem;
    animation: pulse 2s ease-in-out infinite;
  }
  
  @keyframes pulse {
    0%, 100% {
      transform: scale(1);
    }
    50% {
      transform: scale(1.05);
    }
  }
  
  .error-code {
    font-size: 3rem;
    font-weight: 700;
    color: #DC2626;
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
    <i class="fas fa-lock text-white text-5xl"></i>
  </div>
  
  <h1 class="error-code">403</h1>
  
  <h2 class="error-title">Akses Ditolak</h2>
  
  <p class="error-message">
    Maaf, Anda tidak memiliki izin untuk mengakses halaman ini. 
    Silakan hubungi administrator untuk mendapatkan akses yang diperlukan.
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