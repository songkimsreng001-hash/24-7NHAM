@extends('layout.frontend')

@section('content')
<div class="container py-4 py-md-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">
            
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <!-- Support Header -->
                <div class="card-header bg-success text-white p-3 p-md-4 d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle bg-white text-success d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px; font-size: 1.5rem;">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold">24/7 NHAM Live Support</h5>
                            <div class="d-flex align-items-center gap-2 small text-white-50 mt-1">
                                <span class="d-inline-block rounded-circle bg-success-subtle" style="width: 8px; height: 8px; background-color: #4ade80;"></span>
                                <span>Support team active • Typically replies in seconds</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('customer.orders.index') }}" class="btn btn-outline-light btn-sm rounded-3">
                        <i class="bi bi-arrow-left me-1"></i> Orders
                    </a>
                </div>

                <!-- Quick Help Chips -->
                <div class="bg-light px-3 py-2 border-bottom d-flex align-items-center gap-2 overflow-auto" style="white-space: nowrap;">
                    <span class="small text-muted fw-semibold flex-shrink-0">Quick Topics:</span>
                    <button type="button" class="btn btn-sm btn-white border rounded-pill text-secondary px-3 py-1 quick-chip" data-text="Hello, I need help with my recent delivery status.">
                        Delivery Delay
                    </button>
                    <button type="button" class="btn btn-sm btn-white border rounded-pill text-secondary px-3 py-1 quick-chip" data-text="I have a question regarding my payment and promo discount.">
                        Payment & Promo
                    </button>
                    <button type="button" class="btn btn-sm btn-white border rounded-pill text-secondary px-3 py-1 quick-chip" data-text="I need assistance contacting my assigned driver.">
                        Driver Contact
                    </button>
                    <button type="button" class="btn btn-sm btn-white border rounded-pill text-secondary px-3 py-1 quick-chip" data-text="I would like to modify my delivery address for an active order.">
                        Change Address
                    </button>
                </div>

                <!-- Chat Messages Body -->
                <div id="supportChatBox" class="card-body p-3 p-md-4" style="height: 440px; overflow-y: auto; background-color: #f8fafc;">
                    <div id="chatLoading" class="text-center py-5 text-muted">
                        <div class="spinner-border spinner-border-sm text-success me-2" role="status"></div>
                        Connecting to live support...
                    </div>
                    <div id="messagesList" class="d-flex flex-column gap-3" style="display: none !important;"></div>
                </div>

                <!-- Input Footer -->
                <div class="card-footer bg-white p-3 border-top">
                    <form id="supportChatForm" class="d-flex align-items-center gap-2">
                        @csrf
                        <input type="text" id="supportMessageInput" class="form-control rounded-pill px-3 py-2 border-1" 
                               placeholder="Type your question or message here..." autocomplete="off" required>
                        <button type="submit" id="supportSendBtn" class="btn btn-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                style="width: 44px; height: 44px; flex-shrink: 0;">
                            <i class="bi bi-send-fill text-white"></i>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatBox = document.getElementById('supportChatBox');
    const messagesList = document.getElementById('messagesList');
    const chatLoading = document.getElementById('chatLoading');
    const chatForm = document.getElementById('supportChatForm');
    const messageInput = document.getElementById('supportMessageInput');
    const currentUserId = {{ auth()->id() }};
    let lastMessageCount = 0;

    function renderMessages(messages) {
        chatLoading.style.display = 'none';
        messagesList.style.display = 'flex';

        if (messages.length === 0) {
            messagesList.innerHTML = `
                <div class="text-center py-4 text-muted">
                    <div class="rounded-circle bg-white p-3 d-inline-block shadow-sm mb-2 text-success">
                        <i class="bi bi-chat-quote fs-3"></i>
                    </div>
                    <p class="small mb-1 fw-bold text-dark">Welcome to 24/7 NHAM Support!</p>
                    <p class="small text-muted mb-0">How can we assist you with your food delivery today?</p>
                </div>
            `;
            return;
        }

        let html = '';
        messages.forEach(msg => {
            const isMe = msg.sender_id === currentUserId;
            const time = new Date(msg.created_at).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            if (isMe) {
                html += `
                    <div class="d-flex justify-content-end">
                        <div class="text-end" style="max-width: 80%;">
                            <div class="bg-success text-white px-3 py-2 rounded-4 shadow-sm text-start" style="border-bottom-right-radius: 4px !important;">
                                ${escapeHtml(msg.message)}
                            </div>
                            <span class="text-muted" style="font-size: 0.7rem;">${time} • You</span>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="d-flex justify-content-start align-items-start gap-2">
                        <div class="rounded-circle bg-white border d-flex align-items-center justify-content-center text-success fw-bold flex-shrink-0" style="width: 32px; height: 32px; font-size: 0.8rem;">
                            <i class="bi bi-headset"></i>
                        </div>
                        <div style="max-width: 80%;">
                            <div class="bg-white border text-dark px-3 py-2 rounded-4 shadow-sm" style="border-bottom-left-radius: 4px !important;">
                                <div class="fw-bold text-success mb-1" style="font-size: 0.75rem;">${escapeHtml(msg.sender ? msg.sender.name : 'NHAM Support')}</div>
                                ${escapeHtml(msg.message)}
                            </div>
                            <span class="text-muted" style="font-size: 0.7rem;">${time}</span>
                        </div>
                    </div>
                `;
            }
        });

        messagesList.innerHTML = html;

        if (messages.length > lastMessageCount) {
            chatBox.scrollTop = chatBox.scrollHeight;
            lastMessageCount = messages.length;
        }
    }

    function escapeHtml(text) {
        const map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return (text || '').replace(/[&<>"']/g, m => map[m]);
    }

    function fetchMessages() {
        fetch('{{ route("support.messages") }}')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    renderMessages(data.messages);
                }
            })
            .catch(err => console.error('Error fetching support chat:', err));
    }

    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const text = messageInput.value.trim();
        if (!text) return;

        messageInput.value = '';

        fetch('{{ route("support.send") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: text })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                fetchMessages();
            }
        })
        .catch(err => console.error('Error sending message:', err));
    });

    document.querySelectorAll('.quick-chip').forEach(btn => {
        btn.addEventListener('click', function() {
            messageInput.value = this.dataset.text;
            messageInput.focus();
        });
    });

    // Initial load and 3.5s polling
    fetchMessages();
    setInterval(fetchMessages, 3500);
});
</script>
@endpush
@endsection

