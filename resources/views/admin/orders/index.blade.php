@extends('admin.layout.app')

@section('title', __('Orders Management'))

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <h3 class="mb-4">{{ __('Orders') }}</h3>

        <!-- Filter Section -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('admin.orders.index') }}" id="filter-form">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="delivery_method" class="form-label">{{ __('Delivery Method') }}</label>
                            <select name="delivery_method" id="delivery_method" class="form-select">
                                <option value="">{{ __('All Delivery Methods') }}</option>
                                <option value="pickup" {{ request('delivery_method') == 'pickup' ? 'selected' : '' }}>{{ __('Pickup') }}</option>
                                <option value="delivery" {{ request('delivery_method') == 'delivery' ? 'selected' : '' }}>{{ __('Delivery') }}</option>
                                <option value="express" {{ request('delivery_method') == 'express' ? 'selected' : '' }}>{{ __('Express') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="payment_method" class="form-label">{{ __('Payment Method') }}</label>
                            <select name="payment_method" id="payment_method" class="form-select">
                                <option value="">{{ __('All Payment Methods') }}</option>
                                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>{{ __('Cash') }}</option>
                                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>{{ __('Card') }}</option>
                                <option value="credit" {{ request('payment_method') == 'credit' ? 'selected' : '' }}>{{ __('Credit') }}</option>
                                <option value="online" {{ request('payment_method') == 'online' ? 'selected' : '' }}>{{ __('Online') }}</option>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <div class="btn-group">
                                <button type="submit" class="btn btn-primary" title="{{ __('Filter') }}" style="width: 40px;">
                                    <i class="fas fa-filter"></i>
                                </button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary" title="{{ __('Clear') }}" style="width: 40px;">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>{{ __('#') }}</th>
                    <th>{{ __('Full Name') }}</th>
                    <th>{{ __('Fin') }}</th>
                    <th>{{ __('Phone') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('City') }}</th>
                    <th>{{ __('Products') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Payment Status') }}</th>
                    <th>{{ __('Credit Card') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    @php
                        $productIds = json_decode($order->product_ids ?? '[]', true);
                        $products = \App\Models\Product::whereIn('id', $productIds)->with('translates')->get();
                        $order->load('creditCard');
                    @endphp
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->full_name }}</td>
                        <td>{{ $order->fin }}</td>
                        <td>{{ $order->phone }}</td>
                        <td>{{ $order->email }}</td>
                        <td>{{ $order->city }}</td>
                        <td>
                            @if($products->count() > 0)
                                <ul class="list-unstyled mb-0">
                                    @foreach($products as $product)
                                        <li>{{ $product->translate()?->title ?? 'Unknown Product' }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span>—</span>
                            @endif
                        </td>
                        <td><span class="badge bg-info">{{ $order->status }}</span></td>
                        <td><span class="badge bg-success">{{ $order->payment_status }}</span></td>
                        <td>{{ $order->creditCard ? $order->creditCard->title . ' (' . $order->creditCard->card_number . ')' : '—' }}</td>
                        <td>
                            <button 
                                class="btn btn-sm btn-primary show-order"
                                data-id="{{ $order->id }}"
                                data-full_name="{{ $order->full_name }}"
                                data-phone="{{ $order->phone }}"
                                data-email="{{ $order->email }}"
                                data-city="{{ $order->city }}"
                                data-status="{{ $order->status }}"
                                data-delivery_method="{{ $order->delivery_method }}"
                                data-payment_method="{{ $order->payment_method }}"
                                data-selected_card="{{ $order->creditCard ? $order->creditCard->title . ' (' . $order->creditCard->card_number . ')' : '—' }}"
                                data-month="{{ $order->month }}"
                                data-monthly_price="{{ $order->monthly_price }}"
                                data-amount="{{ $order->amount }}"
                                data-address="{{ $order->address }}"
                                data-text="{{ $order->text }}"
                                data-product="{{ $products->first() ? ($products->first()->translate()?->title ?? 'Unknown Product') : '—' }}"
                            >
                                Show
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 mb-4 d-flex justify-content-center">
            {{ $orders->links('vendor.pagination.bootstrap-4') }}
        </div>

    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Order Details</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div id="order-details">Loading...</div>
          </div>
      </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    // Auto-submit filter form when dropdown values change
    document.getElementById('delivery_method').addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });
    
    document.getElementById('payment_method').addEventListener('change', function() {
        document.getElementById('filter-form').submit();
    });

    document.querySelectorAll(".show-order").forEach(btn => {
        btn.addEventListener("click", function() {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0" style="color: black;">Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>ID:</strong> ${this.dataset.id}</p>
                                <p><strong>Full Name:</strong> ${this.dataset.full_name || '-'}</p>
                                <p><strong>Phone:</strong> ${this.dataset.phone || '-'}</p>
                                <p><strong>Email:</strong> ${this.dataset.email || '-'}</p>
                                <p><strong>City:</strong> ${this.dataset.city || '-'}</p>
                                <p><strong>Address:</strong> ${this.dataset.address || '-'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0" style="color: black;">Order Details</h6>
                            </div>
                            <div class="card-body">
                                <p><strong>Status:</strong> <span class="badge bg-info">${this.dataset.status || '-'}</span></p>
                                <p><strong>Payment Status:</strong> <span class="badge ${this.dataset.payment_status === 'paid' ? 'bg-success' : 'bg-warning'}">${this.dataset.payment_status || 'unpaid'}</span></p>
                                <p><strong>Delivery Method:</strong> ${this.dataset.delivery_method || '-'}</p>
                                <p><strong>Payment Method:</strong> ${this.dataset.payment_method || '-'}</p>
                                <p><strong>Selected Card:</strong> ${this.dataset.selected_card || 'Not specified'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0" style="color: black;">Product Information</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <td>${this.dataset.product || 'N/A'}</td>
                                <td>${this.dataset.month ? this.dataset.month + ' months' : '-'}</td>
                                <td>${this.dataset.monthly_price ? parseFloat(this.dataset.monthly_price).toFixed(2) + ' AZN' : '-'}</td>
                                <td>${this.dataset.amount ? parseFloat(this.dataset.amount).toFixed(2) + ' AZN' : '-'}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                ${this.dataset.text ? `
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Additional Notes</h6>
                    </div>
                    <div class="card-body">
                        <p>${this.dataset.text}</p>
                    </div>
                </div>` : ''}
            `;
            document.getElementById("order-details").innerHTML = html;
            new bootstrap.Modal(document.getElementById('orderModal')).show();
        });
    });
});
</script>
@endpush
