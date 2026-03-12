<?php
// models/VenteModel.php
require_once 'config/Database.php';
require_once 'models/entities/Vente.php';
require_once 'models/ProductModel.php';

class VenteModel {

    private $db;
    private $productModel;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->productModel = new ProductModel();
    }

    /**
     * Récupérer toutes les ventes
     */
    public function getAllVentes() {
        $sql = "SELECT v.*, u.nom as vendeur_nom 
                FROM ventes v
                LEFT JOIN users u ON v.user_id = u.id
                ORDER BY v.date_vente DESC";

        $stmt = $this->db->query($sql);
        $data = $stmt->fetchAll();

        $ventes = [];
        foreach ($data as $row) {
            $ventes[] = new Vente($row);
        }
        return $ventes;
    }

    /**
     * Récupérer une vente par son ID
     */
    public function getVenteById($id) {
        $sql = "SELECT v.*, u.nom as vendeur_nom 
                FROM ventes v
                LEFT JOIN users u ON v.user_id = u.id
                WHERE v.id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        $data = $stmt->fetch();

        if ($data) {
            return new Vente($data);
        }
        return null;
    }

    /**
     * Récupérer les ventes d'un vendeur spécifique
     */
    public function getVentesByVendeur($userId) {
        $sql = "SELECT v.*, u.nom as vendeur_nom 
                FROM ventes v
                LEFT JOIN users u ON v.user_id = u.id
                WHERE v.user_id = ?
                ORDER BY v.date_vente DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $data = $stmt->fetchAll();

        $ventes = [];
        foreach ($data as $row) {
            $ventes[] = new Vente($row);
        }
        return $ventes;
    }

    /**
     * Récupérer les ventes avec leurs détails
     */
    public function getVentesWithDetails() {
        $sql = "SELECT v.*, u.nom as vendeur_nom,
                       vd.produit_id, vd.quantite, vd.prix_unitaire,
                       p.nom as produit_nom
                FROM ventes v
                LEFT JOIN users u ON v.user_id = u.id
                LEFT JOIN vente_details vd ON v.id = vd.vente_id
                LEFT JOIN produits p ON vd.produit_id = p.id
                ORDER BY v.date_vente DESC";

        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll();

        // Organiser les résultats par vente
        $ventesArray = [];
        foreach ($results as $row) {
            $venteId = $row['id'];
            if (!isset($ventesArray[$venteId])) {
                $vente = new Vente($row);
                $ventesArray[$venteId] = $vente;
            }

            if ($row['produit_id']) {
                $produit = [
                    'produit_id' => $row['produit_id'],
                    'produit_nom' => $row['produit_nom'],
                    'quantite' => $row['quantite'],
                    'prix_unitaire' => $row['prix_unitaire']
                ];
                $ventesArray[$venteId]->addProduit($produit);
            }
        }

        return array_values($ventesArray);
    }

    /**
     * Créer une nouvelle vente
     */
    public function createVente($userId, $clientNom, $total, $paiement, $produits) {
        try {
            $this->db->beginTransaction();

            // Insérer la vente
            $sqlVente = "INSERT INTO ventes (user_id, client_nom, total, paiement) 
                         VALUES (?, ?, ?, ?)";
            $stmtVente = $this->db->prepare($sqlVente);
            $stmtVente->execute([$userId, $clientNom, $total, $paiement]);

            $venteId = $this->db->lastInsertId();

            // Insérer les détails de la vente
            $sqlDetail = "INSERT INTO vente_details (vente_id, produit_id, quantite, prix_unitaire) 
                          VALUES (?, ?, ?, ?)";
            $stmtDetail = $this->db->prepare($sqlDetail);

            foreach ($produits as $produit) {
                $stmtDetail->execute([
                    $venteId,
                    $produit['id'],
                    $produit['quantite'],
                    $produit['prix']
                ]);

                // Mettre à jour le stock
                $this->productModel->updateStock($produit['id'], $produit['quantite']);
            }

            $this->db->commit();
            return $venteId;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Annuler une vente
     */
    public function annulerVente($id) {
        try {
            $this->db->beginTransaction();

            // Récupérer les détails pour remettre en stock
            $sqlDetails = "SELECT * FROM vente_details WHERE vente_id = ?";
            $stmtDetails = $this->db->prepare($sqlDetails);
            $stmtDetails->execute([$id]);
            $details = $stmtDetails->fetchAll();

            // Remettre en stock
            foreach ($details as $detail) {
                $product = $this->productModel->getProductById($detail['produit_id']);
                if ($product) {
                    // On ne peut pas utiliser updateStock car elle soustrait, donc requête directe
                    $sqlStock = "UPDATE produits SET stock = stock + ? WHERE id = ?";
                    $stmtStock = $this->db->prepare($sqlStock);
                    $stmtStock->execute([$detail['quantite'], $detail['produit_id']]);
                }
            }

            // Mettre à jour le statut de la vente
            $sql = "UPDATE ventes SET statut = 'annulé' WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);

            $this->db->commit();
            return true;

        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Statistiques des ventes
     */
    public function getStats() {
        $stats = [];

        // Chiffre d'affaires total
        $stmt = $this->db->query("SELECT SUM(total) as ca FROM ventes WHERE statut = 'payé'");
        $stats['ca_total'] = $stmt->fetch()['ca'] ?? 0;

        // Nombre de ventes
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM ventes");
        $stats['nb_ventes'] = $stmt->fetch()['total'];

        // Ventes par statut
        $stmt = $this->db->query("SELECT statut, COUNT(*) as count FROM ventes GROUP BY statut");
        $stats['par_statut'] = $stmt->fetchAll();

        // Ventes du jour
        $stmt = $this->db->query("SELECT COUNT(*) as total, SUM(total) as ca FROM ventes WHERE DATE(date_vente) = CURDATE()");
        $row = $stmt->fetch();
        $stats['ventes_aujourdhui'] = $row['total'] ?? 0;
        $stats['ca_aujourdhui'] = $row['ca'] ?? 0;

        return $stats;
    }
}
?>