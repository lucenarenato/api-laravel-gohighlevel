<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RewardsClaimed extends Mailable
{
    use Queueable, SerializesModels;
    private $claimed;

    /**
     * Create a new message instance.
     */
    public function __construct($claimed)
    {
        $this->claimed = $claimed;
        // 
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rewards Claimed',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.rewards.claimed',
            with: [
                'member_id' => $this->claimed->member->id,
                'member_name' => $this->claimed->member->name,
                'reward_id' => $this->claimed->reward->id,
                'reward_name' => $this->claimed->reward->name,
                'admin' => $this->claimed->admin
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
