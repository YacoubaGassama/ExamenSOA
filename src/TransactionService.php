<?php
declare(strict_types=1);

use App\Dto\Transaction;
use App\Dto\TransactionInput;
use App\Message\CreateTransactionRequest;
use App\Message\CreateTransactionResponse;
use App\Message\GetTransactionRequest;
use App\Message\GetTransactionResponse;
use App\Message\UpdateTransactionRequest;
use App\Message\UpdateTransactionResponse;
use App\Message\DeleteTransactionRequest;
use App\Message\DeleteTransactionResponse;
use PDO;

class TransactionService
{
    private PDO $pdo;

    public function __construct()
    {
        // Connection MySQL (remplacer les infos de connexion avec tes propres données)
        $this->pdo = new PDO('mysql:host=localhost;dbname=transactions;charset=utf8', 'root', 'root');  // Remplacer par les bonnes infos
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Créer la table si elle n'existe pas déjà
        $this->pdo->exec('
            CREATE TABLE IF NOT EXISTS transactions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                date DATE NOT NULL,
                type ENUM("DEPOT", "RETRAIT", "TRANSFERT") NOT NULL,
                montant DECIMAL(10, 2) NOT NULL,
                tel_expediteur VARCHAR(15) NOT NULL,
                tel_destinataire VARCHAR(15) NOT NULL,
                nom_expediteur VARCHAR(100) NOT NULL,
                nom_destinataire VARCHAR(100) NOT NULL
            )
        ');
    }

    public function createTransaction(CreateTransactionRequest $request): CreateTransactionResponse
    {
        $input = $request->transaction;
        $stmt = $this->pdo->prepare('
            INSERT INTO transactions (date, type, montant, tel_expediteur, tel_destinataire, nom_expediteur, nom_destinataire)
            VALUES (:date, :type, :montant, :tel_exp, :tel_dest, :nom_exp, :nom_dest)
        ');
        $stmt->execute([
            ':date' => $input->date,
            ':type' => $input->type,
            ':montant' => $input->montant,
            ':tel_exp' => $input->tel_expediteur,
            ':tel_dest' => $input->tel_destinataire,
            ':nom_exp' => $input->nom_expediteur,
            ':nom_dest' => $input->nom_destinataire,
        ]);
        
        // Récupère l'ID du dernier enregistrement inséré
        $id = (int)$this->pdo->lastInsertId();
        $response = new CreateTransactionResponse();
        $response->success = true;
        return $response;
    }

    public function getAllTransactions(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM transactions');
        $transactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $transactionObj = new Transaction();
            $transactionObj->id = (int)$row['id'];
            $transactionObj->date = $row['date'];
            $transactionObj->type = $row['type'];
            $transactionObj->montant = (float)$row['montant'];
            $transactionObj->tel_expediteur = $row['tel_expediteur'];
            $transactionObj->tel_destinataire = $row['tel_destinataire'];
            $transactionObj->nom_expediteur = $row['nom_expediteur'];
            $transactionObj->nom_destinataire = $row['nom_destinataire'];
            $transactions[] = $transactionObj;
        }
        return $transactions;
    }


    public function updateTransaction(UpdateTransactionRequest $request): UpdateTransactionResponse
    {
        $response = new UpdateTransactionResponse();
        $tr = $request->transaction;
        $stmt = $this->pdo->prepare('
            UPDATE transactions SET
                date = :date,
                type = :type,
                montant = :montant,
                tel_expediteur = :tel_exp,
                tel_destinataire = :tel_dest,
                nom_expediteur = :nom_exp,
                nom_destinataire = :nom_dest
            WHERE id = :id
        ');
        $stmt->execute([
            ':id' => $tr->id,
            ':date' => $tr->date,
            ':type' => $tr->type,
            ':montant' => $tr->montant,
            ':tel_exp' => $tr->tel_expediteur,
            ':tel_dest' => $tr->tel_destinataire,
            ':nom_exp' => $tr->nom_expediteur,
            ':nom_dest' => $tr->nom_destinataire,
        ]);
        
        if ($stmt->rowCount() === 0) {
            throw new SoapFault('Client', 'Transaction introuvable');
        }

        $response->success = true;
        return $response;
    }

    public function deleteTransaction(DeleteTransactionRequest $request): DeleteTransactionResponse
    {
        $stmt = $this->pdo->prepare('DELETE FROM transactions WHERE id = :id');
        $stmt->execute([':id' => $request->transactionId]);

        $response = new DeleteTransactionResponse();
        $response->success = $stmt->rowCount() > 0;
        return $response;
    }

    public function listTransactions(): array
    {
        $stmt = $this->pdo->query('SELECT * FROM transactions');
        $transactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $transactionObj = new Transaction();
            $transactionObj->id = (int)$row['id'];
            $transactionObj->date = $row['date'];
            $transactionObj->type = $row['type'];
            $transactionObj->montant = (float)$row['montant'];
            $transactionObj->tel_expediteur = $row['tel_expediteur'];
            $transactionObj->tel_destinataire = $row['tel_destinataire'];
            $transactionObj->nom_expediteur = $row['nom_expediteur'];
            $transactionObj->nom_destinataire = $row['nom_destinataire'];
            $transactions[] = $transactionObj;
        }
        return $transactions;
    }
}
