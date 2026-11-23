{{-- Cancel control for a customer order. Vars: $order, $compact (bool, optional) --}}
@if($order->canBeCancelled())
    <button type="button" onclick="document.getElementById('cancel-dlg-{{ $order->id }}').showModal()"
            class="w-full sm:w-auto justify-center px-space-md py-space-xs bg-error-container text-on-error-container rounded-full font-label-lg text-label-lg hover:bg-error hover:text-on-error transition-all flex items-center gap-1.5">
        <span class="material-symbols-outlined text-[18px]">cancel</span><span>Cancel Order</span>
    </button>
    <dialog id="cancel-dlg-{{ $order->id }}" class="w-[calc(100%-2rem)] max-w-md rounded-3xl p-0 backdrop:bg-primary/50 backdrop:backdrop-blur-sm shadow-[0_24px_48px_-12px_rgba(42,23,12,0.3)]"
            onclick="if(event.target===this) this.close()">
        <form method="POST" action="{{ route('orders.cancel', $order->order_number) }}" class="p-6 flex flex-col gap-4 bg-surface">
            @csrf
            <div class="flex items-start gap-3">
                <div class="w-11 h-11 shrink-0 rounded-full bg-error-container text-on-error-container flex items-center justify-center"><span class="material-symbols-outlined">cancel</span></div>
                <div>
                    <h3 class="font-headline-sm text-headline-sm text-primary">Cancel order #{{ $order->order_number }}?</h3>
                    <p class="font-body-sm text-body-sm text-on-surface-variant mt-1">{{ setting('cancel_note', 'You can cancel until our bakers start preparing your order.') }}</p>
                </div>
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="font-label-md text-label-md text-primary" for="reason-{{ $order->id }}">Reason (optional)</label>
                <input id="reason-{{ $order->id }}" name="reason" maxlength="180" placeholder="e.g. Ordered by mistake" class="h-11 px-4 rounded-xl bg-surface-container-low font-body-md text-body-md focus:outline-none focus:ring-2 focus:ring-secondary/40">
            </div>
            <div class="flex flex-col-reverse sm:flex-row gap-2 sm:justify-end pt-1">
                <button type="button" onclick="this.closest('dialog').close()" class="h-11 px-5 rounded-full bg-surface-container text-primary font-label-lg text-label-lg hover:bg-surface-container-high">Keep my order</button>
                <button type="submit" class="h-11 px-5 rounded-full bg-error text-on-error font-label-lg text-label-lg hover:opacity-90">Yes, cancel this order</button>
            </div>
        </form>
    </dialog>
@elseif($order->cancellationLocked())
    <span class="w-full sm:w-auto justify-center px-space-md py-space-xs bg-surface-container text-on-surface-variant/60 rounded-full font-label-lg text-label-lg flex items-center gap-1.5 cursor-not-allowed select-none"
          aria-disabled="true" title="{{ setting('cancel_locked_note', 'Preparation has started — this order can no longer be cancelled.') }}">
        <span class="material-symbols-outlined text-[18px]">lock</span><span>Cancel unavailable</span>
    </span>
    @unless($compact ?? false)
        <span class="w-full sm:w-auto font-body-sm text-[11px] text-on-surface-variant text-center sm:text-left">{{ setting('cancel_locked_note', 'Preparation has started — this order can no longer be cancelled.') }}</span>
    @endunless
@endif
