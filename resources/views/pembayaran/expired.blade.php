@extends('layouts.app')

@section('title', 'Subscription Expired')

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Subscription Required</h1>
        </div>

        <div class="section-body">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <div class="mb-4">
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 80px;"></i>
                            </div>
                            
                            <h3 class="mb-3">Subscription Expired or Inactive</h3>
                            
                            @if(session('error'))
                                <div class="alert alert-danger mb-4">
                                    {{ session('error') }}
                                </div>
                            @endif
                            
                            <p class="text-muted mb-4">
                                Your subscription has expired or is inactive. Please contact the administrator to renew your subscription and continue using the system.
                            </p>

                            <div class="mb-4">
                                <p class="mb-2"><strong>What you can do:</strong></p>
                                <ul class="list-unstyled">
                                    <li class="mb-2">📞 Contact admin to renew your subscription</li>
                                    <li class="mb-2">💳 Choose a subscription plan that fits your needs</li>
                                    <li class="mb-2">🔄 Continue enjoying our services after renewal</li>
                                </ul>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('landing') }}" class="btn btn-primary btn-lg px-5">
                                    <i class="fas fa-home mr-2"></i> Back to Home
                                </a>
                            </div>

                            @if(auth()->user() && auth()->user()->role_id != 1)
                                <div class="mt-4">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-muted">
                                            <i class="fas fa-sign-out-alt mr-1"></i> Logout
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>

                    @php
                        $user = auth()->user();
                        $owner = null;
                        $subscription = null;
                        
                        if ($user && $user->role_id != 1) {
                            $owner = \App\Models\Owner::where('pengguna_id', $user->id)->first();
                            if ($owner) {
                                $subscription = \App\Models\Langganan::where('owner_id', $owner->id)
                                    ->orderBy('created_at', 'desc')
                                    ->first();
                            }
                        }
                    @endphp

                    @if($subscription)
                        <div class="card mt-4">
                            <div class="card-header">
                                <h4>Your Subscription Details</h4>
                            </div>
                            <div class="card-body">
                                <table class="table table-borderless">
                                    <tr>
                                        <td width="40%"><strong>Package:</strong></td>
                                        <td>{{ $subscription->tipeLayanan->nama ?? 'Trial' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($subscription->is_active == 1 && $subscription->is_trial == 0)
                                                <span class="badge badge-success">Active</span>
                                            @elseif($subscription->is_trial == 1 && $subscription->is_active == 0)
                                                <span class="badge badge-warning">Trial (Expired)</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Start Date:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($subscription->started_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>End Date:</strong></td>
                                        <td>{{ \Carbon\Carbon::parse($subscription->end_date)->format('d M Y') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Days Remaining:</strong></td>
                                        <td>
                                            @php
                                                $daysLeft = \Carbon\Carbon::now()->diffInDays(\Carbon\Carbon::parse($subscription->end_date), false);
                                            @endphp
                                            @if($daysLeft > 0)
                                                {{ $daysLeft }} days
                                            @else
                                                <span class="text-danger">Expired {{ abs($daysLeft) }} days ago</span>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
