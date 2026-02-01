@extends('layouts.error')

@section('title', 'Server Maintenance')

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
    background: linear-gradient(135deg, #6B7280 0%, #9CA3AF 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 2rem;
    animation: rotate 3s linear infinite;
  }
  
  @keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
  }
  
  .error-code {
    font-size: 3rem;
    font-weight: 700;
    color: #6B7280;
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
    <i class="fas fa-tools text-white text-5xl"></i>
  </div>
  
  <h1 class="error-code">503</h1>
  
  <h2 class="error-title">Service Unavailable</h2>
  
  <p class="error-message">
    Situs sedang dalam maintenance sementara. Tim kami sedang melakukan pemeliharaan sistem. 
    Silakan coba lagi dalam beberapa saat.
  </p>
  
  <div class="error-actions">
    <button onclick="location.reload()" class="btn-secondary">
      <i class="fas fa-redo mr-2"></i>
      Coba Lagi
    </button>
    
    <a href="{{ route('dashboard') }}" class="btn-primary">
      <i class="fas fa-home mr-2"></i>
      Dashboard
    </a>
  </div>
</div>
@endsection