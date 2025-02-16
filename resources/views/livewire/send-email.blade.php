<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <input type="email" wire:model="to" placeholder="Email người nhận">
    <input type="text" wire:model="subject" placeholder="Tiêu đề">
    <textarea wire:model="body" placeholder="Nội dung email"></textarea>
    <button wire:click="send">Gửi Email</button>
</div>
