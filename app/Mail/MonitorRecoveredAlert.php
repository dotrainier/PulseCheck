<?php

namespace App\Mail;

use App\Models\Incident;
use App\Models\Monitor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MonitorRecoveredAlert extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Monitor $monitor,
        public Incident $incident,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[RECOVERED] {$this->monitor->name} is back online",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.monitor-recovered',
        );
    }
}
