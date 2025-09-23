<?php
require_once 'config/database.php';
require_once 'config/config.php';

class Notification {
    private $conn;
    private $table_name = "notifications";

    public $id;
    public $user_id;
    public $supplier_id;
    public $title;
    public $message;
    public $type;
    public $is_read;
    public $created_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Crear notificación
    public function create() {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, supplier_id, title, message, type) 
                  VALUES (:user_id, :supplier_id, :title, :message, :type)";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->message = htmlspecialchars(strip_tags($this->message));

        $stmt->bindParam(':user_id', $this->user_id);
        $stmt->bindParam(':supplier_id', $this->supplier_id);
        $stmt->bindParam(':title', $this->title);
        $stmt->bindParam(':message', $this->message);
        $stmt->bindParam(':type', $this->type);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Enviar notificación por email
    public function sendEmail($to_email, $to_name = '') {
        $subject = $this->title;
        $message = $this->message;
        
        // Headers del email
        $headers = "MIME-Version: 1.0" . "\r\n";
        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
        $headers .= "From: " . FROM_NAME . " <" . FROM_EMAIL . ">" . "\r\n";
        $headers .= "Reply-To: " . FROM_EMAIL . "\r\n";
        
        // Template HTML del email
        $html_message = $this->getEmailTemplate($subject, $message, $to_name);
        
        return mail($to_email, $subject, $html_message, $headers);
    }

    // Template HTML para emails
    private function getEmailTemplate($subject, $message, $to_name) {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>$subject</title>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f8f9fa; padding: 30px; border-radius: 0 0 10px 10px; }
                .footer { text-align: center; margin-top: 20px; color: #666; font-size: 12px; }
                .btn { display: inline-block; padding: 10px 20px; background: #667eea; color: white; text-decoration: none; border-radius: 5px; margin: 10px 0; }
                .btn:hover { background: #5a6fd8; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h2>Sistema de Procurement</h2>
                    <p>Notificación del Sistema</p>
                </div>
                <div class='content'>
                    <h3>$subject</h3>
                    <p>Hola" . ($to_name ? " $to_name" : "") . ",</p>
                    <p>$message</p>
                    <p>Para más información, accede al sistema de procurement.</p>
                    <a href='" . SITE_URL . "' class='btn'>Acceder al Sistema</a>
                </div>
                <div class='footer'>
                    <p>Este es un mensaje automático del Sistema de Procurement. No responder a este email.</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }

    // Notificar nueva orden a proveedores
    public static function notifyNewOrder($db, $order_id, $supplier_ids) {
        $query = "SELECT title, order_number FROM purchase_orders WHERE id = :order_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':order_id', $order_id);
        $stmt->execute();
        $order = $stmt->fetch(PDO::FETCH_ASSOC);

        if($order) {
            $notification = new Notification($db);
            $notification->title = "Nueva Orden de Compra: " . $order['order_number'];
            $notification->message = "Se ha asignado una nueva orden de compra: " . $order['title'] . ". Por favor, revisa los detalles y envía tu cotización.";
            $notification->type = 'info';

            foreach($supplier_ids as $supplier_id) {
                $notification->supplier_id = $supplier_id;
                $notification->user_id = null;
                $notification->create();
            }
        }
    }

    // Notificar aprobación de cotización
    public static function notifyQuotationApproved($db, $quotation_id) {
        $query = "SELECT q.*, s.email, s.contact_name, po.order_number 
                  FROM quotations q 
                  INNER JOIN suppliers s ON q.supplier_id = s.id 
                  INNER JOIN purchase_orders po ON q.order_id = po.id 
                  WHERE q.id = :quotation_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':quotation_id', $quotation_id);
        $stmt->execute();
        $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

        if($quotation) {
            $notification = new Notification($db);
            $notification->title = "Cotización Aprobada - Orden " . $quotation['order_number'];
            $notification->message = "Su cotización ha sido aprobada para la orden " . $quotation['order_number'] . ". Puede proceder con la entrega.";
            $notification->type = 'success';
            $notification->supplier_id = $quotation['supplier_id'];
            $notification->user_id = null;
            
            if($notification->create()) {
                $notification->sendEmail($quotation['email'], $quotation['contact_name']);
            }
        }
    }

    // Notificar rechazo de cotización
    public static function notifyQuotationRejected($db, $quotation_id, $reason = '') {
        $query = "SELECT q.*, s.email, s.contact_name, po.order_number 
                  FROM quotations q 
                  INNER JOIN suppliers s ON q.supplier_id = s.id 
                  INNER JOIN purchase_orders po ON q.order_id = po.id 
                  WHERE q.id = :quotation_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':quotation_id', $quotation_id);
        $stmt->execute();
        $quotation = $stmt->fetch(PDO::FETCH_ASSOC);

        if($quotation) {
            $notification = new Notification($db);
            $notification->title = "Cotización Rechazada - Orden " . $quotation['order_number'];
            $notification->message = "Su cotización para la orden " . $quotation['order_number'] . " ha sido rechazada." . ($reason ? " Razón: " . $reason : "");
            $notification->type = 'error';
            $notification->supplier_id = $quotation['supplier_id'];
            $notification->user_id = null;
            
            if($notification->create()) {
                $notification->sendEmail($quotation['email'], $quotation['contact_name']);
            }
        }
    }

    // Notificar aprobación de proveedor
    public static function notifySupplierApproved($db, $supplier_id) {
        $query = "SELECT email, contact_name, company_name FROM suppliers WHERE id = :supplier_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':supplier_id', $supplier_id);
        $stmt->execute();
        $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

        if($supplier) {
            $notification = new Notification($db);
            $notification->title = "Cuenta de Proveedor Aprobada";
            $notification->message = "Su cuenta de proveedor para " . $supplier['company_name'] . " ha sido aprobada. Ya puede acceder al portal y recibir órdenes de compra.";
            $notification->type = 'success';
            $notification->supplier_id = $supplier_id;
            $notification->user_id = null;
            
            if($notification->create()) {
                $notification->sendEmail($supplier['email'], $supplier['contact_name']);
            }
        }
    }

    // Obtener notificaciones de un usuario
    public function getByUser($user_id, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = :user_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener notificaciones de un proveedor
    public function getBySupplier($supplier_id, $limit = 50, $offset = 0) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE supplier_id = :supplier_id 
                  ORDER BY created_at DESC 
                  LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':supplier_id', $supplier_id);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Marcar como leída
    public function markAsRead() {
        $query = "UPDATE " . $this->table_name . " 
                  SET is_read = 1 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $this->id);

        if($stmt->execute()) {
            return true;
        }
        return false;
    }

    // Contar notificaciones no leídas
    public function countUnread($user_id = null, $supplier_id = null) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE is_read = 0";
        
        if($user_id) {
            $query .= " AND user_id = :user_id";
        } elseif($supplier_id) {
            $query .= " AND supplier_id = :supplier_id";
        }

        $stmt = $this->conn->prepare($query);
        
        if($user_id) {
            $stmt->bindParam(':user_id', $user_id);
        } elseif($supplier_id) {
            $stmt->bindParam(':supplier_id', $supplier_id);
        }
        
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['count'];
    }
}
?>
