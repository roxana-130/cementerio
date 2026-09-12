<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class CorreoController extends Controller
{
    /** Nombres de mes en español, usados para formatear fechaServicio en el correo. */
    private const MESES = [
        1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
        5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
        9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre',
    ];

    /** Tipos de servicio validos para la ficha enviada por correo. */
    private const TIPOS_SERVICIO = ['Inhumación', 'Exhumación', 'Cremación', 'Anexión'];

    /**
     * Envia por correo (via Brevo) la ficha de servicio funerario de un difunto:
     * ubicacion, fecha/hora del servicio y tipo de servicio.
     * Llamado por fetch() desde el modal de agenda/show.blade.php.
     */
    public function enviarUbicacion(Request $request)
    {
        // 1. Validar los datos que vienen desde JavaScript.
        // Si falla, Laravel devuelve automaticamente 422 con { message, errors }.
        $request->validate([
            'nombreDifunto'   => 'required|string|max:150',
            'ubicacionFisica' => 'required|string',
            'correoDestino'   => 'required|email',
            'fechaServicio'   => 'required|date',
            'horaServicio'    => 'required|date_format:H:i',
            'tipoServicio'    => ['required', Rule::in(self::TIPOS_SERVICIO)],
        ]);

        $apiKey = env('BREVO_API_KEY');

        $nombreDifunto   = htmlspecialchars($request->nombreDifunto, ENT_QUOTES, 'UTF-8');
        $ubicacionFisica = htmlspecialchars($request->ubicacionFisica, ENT_QUOTES, 'UTF-8');
        $tipoServicio    = htmlspecialchars($request->tipoServicio, ENT_QUOTES, 'UTF-8');

        $fecha = Carbon::parse($request->fechaServicio);
        $fechaFormateada = $fecha->day . ' de ' . self::MESES[(int) $fecha->month] . ' de ' . $fecha->year;

        $horaFormateada = Carbon::createFromFormat('H:i', $request->horaServicio)->format('h:i A');

        $asunto = "Ubicación de {$nombreDifunto} en Cementerio General";

        $htmlContent = "
            <div style=\"font-family: Arial, Helvetica, sans-serif; max-width: 560px; margin: 0 auto; background: #ffffff;\">
                <div style=\"background-color: #113615; padding: 24px 28px;\">
                    <h1 style=\"color: #ffffff; margin: 0; font-size: 20px;\">Cementerio General de Sacaba</h1>
                </div>
                <div style=\"padding: 28px;\">
                    <h2 style=\"color: #113615; font-size: 18px; margin-top: 0; border-bottom: 2px solid #a3e635; padding-bottom: 8px;\">
                        Ficha de Servicio Funerario
                    </h2>
                    <table style=\"width: 100%; border-collapse: collapse; margin-top: 16px;\">
                        <tr>
                            <td style=\"color: #6c757d; padding: 10px 0; border-bottom: 1px solid #eee; width: 40%;\">Difunto</td>
                            <td style=\"font-weight: bold; color: #212529; padding: 10px 0; border-bottom: 1px solid #eee;\">{$nombreDifunto}</td>
                        </tr>
                        <tr>
                            <td style=\"color: #6c757d; padding: 10px 0; border-bottom: 1px solid #eee;\">Ubicación</td>
                            <td style=\"font-weight: bold; color: #113615; padding: 10px 0; border-bottom: 1px solid #eee;\">{$ubicacionFisica}</td>
                        </tr>
                        <tr>
                            <td style=\"color: #6c757d; padding: 10px 0; border-bottom: 1px solid #eee;\">Fecha del Servicio</td>
                            <td style=\"color: #212529; padding: 10px 0; border-bottom: 1px solid #eee;\">{$fechaFormateada}</td>
                        </tr>
                        <tr>
                            <td style=\"color: #6c757d; padding: 10px 0; border-bottom: 1px solid #eee;\">Hora del Servicio</td>
                            <td style=\"color: #212529; padding: 10px 0; border-bottom: 1px solid #eee;\">{$horaFormateada}</td>
                        </tr>
                        <tr>
                            <td style=\"color: #6c757d; padding: 10px 0;\">Tipo de Servicio</td>
                            <td style=\"padding: 10px 0;\">
                                <span style=\"background: #f0f7f1; color: #113615; padding: 4px 10px; border-radius: 4px; font-weight: bold; font-size: 13px;\">{$tipoServicio}</span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div style=\"background-color: #f8f9fa; padding: 20px 28px; border-top: 1px solid #e5e5e5;\">
                    <p style=\"font-size: 12px; color: #6c757d; margin: 0 0 4px 0;\"><strong>Cementerio General de Sacaba</strong></p>
                    <p style=\"font-size: 12px; color: #6c757d; margin: 0;\">
                        Dirección: [Completar dirección] · Teléfono: [Completar teléfono]<br>
                        Este correo fue generado automáticamente desde la plataforma de gestión del cementerio.
                    </p>
                </div>
            </div>
        ";

        $respuesta = Http::withHeaders([
            'accept'       => 'application/json',
            'api-key'      => $apiKey,  
            'content-type' => 'application/json',
        ])->post('https://api.brevo.com/v3/smtp/email', [
            'sender' => [
                'name'  => 'Cementerio General de Sacaba',
                'email' => 'torrezquelcaroxanaolivia@gmail.com',
            ],
            'to' => [
                ['email' => $request->correoDestino],
            ],
            'subject'     => $asunto,
            'htmlContent' => $htmlContent,
        ]);

        if ($respuesta->status() === 201) {
            return response()->json([
                'success' => true,
                'message' => 'Correo enviado correctamente',
            ]);
        }

        Log::error('Fallo el envio de correo via Brevo.', [
            'status'   => $respuesta->status(),
            'respuesta' => $respuesta->json(),
            'correoDestino' => $request->correoDestino,
        ]);

        return response()->json([
            'success' => false,
            'error'   => 'No se pudo enviar el correo. Intenta nuevamente.',
        ], 502);
    }
}
