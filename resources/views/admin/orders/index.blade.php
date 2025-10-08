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
                            $products = \App\Models\Product::whereIn('id', $productIds)->get();
                        @endphp
                        @if($products->count() > 0)
                            <ul class="list-unstyled mb-0">
                                @foreach($products as $product)
                                    <li>{{ $product->name ?? 'Unknown Product' }}</li>
                                @endforeach
                            </ul>
                        @else
                            —
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
                            data-payment_status="{{ $order->payment_status }}"
                            data-delivery_method="{{ $order->delivery_method }}"
                            data-payment_method="{{ $order->payment_method }}"
                            data-selected_card="{{ $order->selected_card }}"
                            data-month="{{ $order->month }}"
                            data-monthly_price="{{ $order->monthly_price }}"
                            data-text="{{ $order->text }}"
                            data-product="{{ $order->product?->name ?? '—' }}"
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
                <p><strong>ID:</strong> ${this.dataset.id}</p>
                <p><strong>Full Name:</strong> ${this.dataset.full_name}</p>
                <p><strong>Phone:</strong> ${this.dataset.phone}</p>
                <p><strong>Email:</strong> ${this.dataset.email}</p>
                <p><strong>City:</strong> ${this.dataset.city}</p>
                <p><strong>Product:</strong> ${this.dataset.product}</p>
                <p><strong>Status:</strong> ${this.dataset.status}</p>
                <p><strong>Payment Status:</strong> ${this.dataset.payment_status}</p>
                <p><strong>Delivery Method:</strong> ${this.dataset.delivery_method ?? '-'}</p>
                <p><strong>Payment Method:</strong> ${this.dataset.payment_method ?? '-'}</p>
                <p><strong>Selected Card:</strong> ${this.dataset.selected_card ?? '-'}</p>
                <p><strong>Month:</strong> ${this.dataset.month ?? '-'}</p>
                <p><strong>Monthly Price:</strong> ${this.dataset.monthly_price ?? '-'}</p>
                <p><strong>Text:</strong> ${this.dataset.text ?? ''}</p>
            `;
            document.getElementById("order-details").innerHTML = html;
            new bootstrap.Modal(document.getElementById('orderModal')).show();
        });
    });
});
</script>
@endpush
