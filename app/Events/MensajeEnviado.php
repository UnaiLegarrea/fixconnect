<?php

namespace App\Events;

use App\Models\Mensaje;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MensajeEnviado implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensaje;

    /**
     * Create a new event instance.
     */
    public function __construct(Mensaje $mensaje)
    {
        $this->mensaje = $mensaje;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->mensaje->chat_id),
        ];
    }

    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'nuevo.mensaje';
    }

    /**
     * Get the data to broadcast.
     *
     * @return array
     */    public function broadcastWith()
    {
        return [
            'id' => $this->mensaje->id,
            'remitente_id' => $this->mensaje->remitente_id,
            'contenido' => $this->mensaje->contenido,
            'leido' => $this->mensaje->leido,
            'created_at' => $this->mensaje->created_at,
            'remitente' => [
                'id' => $this->mensaje->remitente->id,
                'nombre' => $this->mensaje->remitente->nombre,
            ]
        ];
    }
}
