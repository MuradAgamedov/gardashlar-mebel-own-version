@extends('admin.layout.app')

@section('title', 'Orders Management')

@section('content')
<div class="page-body">
    <div class="container-fluid">
        <h3 class="mb-4">Orders</h3>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>City</th>
                    <th>Products</th>
                    <th>Status</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->full_name }}</td>
                    <td>{{ $order->phone }}</td>
                    <td>{{ $order->email }}</td>
                    <td>{{ $order->city }}</td>
                    <td>
                        @php
                            $productIds = json_decode($order->product_ids ?? '[]', true);
                            $products = \App\Models\Product::whereIn('id', $productIds)->with('translates')->get();
                        @endphp
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
                            data-selected_card="{{ $order->selected_card }}"
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

        <div class="mt-3">
            {{ $orders->links() }}
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
    document.querySelectorAll(".show-order").forEach(btn => {
        btn.addEventListener("click", function() {
            let html = `
                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Customer Information</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>ID:</strong> ${this.dataset.id}</p>
                                <p class="mb-2"><strong>Full Name:</strong> ${this.dataset.full_name || '-'}</p>
                                <p class="mb-2"><strong>Phone:</strong> ${this.dataset.phone || '-'}</p>
                                <p class="mb-2"><strong>Email:</strong> ${this.dataset.email || '-'}</p>
                                <p class="mb-2"><strong>City:</strong> ${this.dataset.city || '-'}</p>
                                <p class="mb-0"><strong>Address:</strong> ${this.dataset.address || '-'}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header bg-light">
                                <h6 class="mb-0">Order Details</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Status:</strong> <span class="badge bg-info">${this.dataset.status || '-'}</span></p>
                                <p class="mb-2"><strong>Payment Status:</strong> <span class="badge ${this.dataset.payment_status === 'paid' ? 'bg-success' : 'bg-warning'}">${this.dataset.payment_status || 'unpaid'}</span></p>
                                <p class="mb-2"><strong>Delivery Method:</strong> ${this.dataset.delivery_method ? this.dataset.delivery_method.charAt(0).toUpperCase() + this.dataset.delivery_method.slice(1) : '-'}</p>
                                <p class="mb-2"><strong>Payment Method:</strong> ${this.dataset.payment_method ? this.dataset.payment_method.charAt(0).toUpperCase() + this.dataset.payment_method.slice(1) : '-'}</p>
                                <p class="mb-0"><strong>Selected Card:</strong> ${this.dataset.selected_card || 'Not specified'}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Product Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Duration</th>
                                        <th>Monthly Price</th>
                                        <th>Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>${this.dataset.product || 'N/A'}</td>
                                        <td>${this.dataset.month ? this.dataset.month + ' months' : '-'}</td>
                                        <td>${this.dataset.monthly_price ? parseFloat(this.dataset.monthly_price).toFixed(2) + ' AZN' : '-'}</td>
                                        <td>${this.dataset.amount ? parseFloat(this.dataset.amount).toFixed(2) + ' AZN' : '-'}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                ${this.dataset.text ? `
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">Additional Notes</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">${this.dataset.text}</p>
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
