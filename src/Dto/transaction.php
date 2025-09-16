<?php
namespace App\Dto;
class Transaction
{
    public int $id;
    public string $date;
    public string $type;
    public float $montant;
    public string $tel_expediteur;
    public string $tel_destinataire;
    public string $nom_expediteur;
    public string $nom_destinataire;
}