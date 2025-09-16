<?php

namespace App;


use App\Dto\Classe;
use App\Dto\ClasseInput;
use App\Dto\Filiere;
use App\Message\AllClassesRequest;
use App\Message\AllClassesResponse;
use App\Message\CreateClasseRequest;
use App\Message\CreateClasseResponse;
use App\Message\CreateFiliereRequest;
use App\Message\CreateFiliereResponse;
use App\Message\SearchClasseByCodeRequest;
use App\Message\SearchClasseByCodeResponse;
use App\Message\SearchClasseByIdRequest;
use App\Message\SearchClasseByIdResponse;
use App\Message\SearchClassesByNameRequest;
use App\Message\SearchClassesByNameResponse;
use SoapFault;


class ScolariteService
{
    /** @var Filiere[] */
    private array $filieres = [];
    /** @var Classe[] */
    private array $classes = [];
    private int $nextFiliereId = 1;
    private int $nextClasseId = 1;


    public function CreateFiliere(CreateFiliereRequest $req): CreateFiliereResponse
    {
        $f = new Filiere();
        $f->id = $this->nextFiliereId++;
        $f->libelle = $req->nom;
        $this->filieres[$f->id] = $f;


        $res = new CreateFiliereResponse();
        $res->filiereId = $f->id;
        return $res;
    }


    public function CreateClasse(CreateClasseRequest $req): CreateClasseResponse
    {
        if (!isset($this->filieres[$req->filiereId])) {
            // Vous pouvez aussi modéliser un fault typé, ici on envoie un SOAP Fault standard
            throw new SoapFault('Client', 'Filiere introuvable: ' . $req->filiereId);
        }
        $ci = $req->classe;
        $c = new Classe();
        $c->id = $this->nextClasseId++;
        $c->code = $ci->code;
        $c->nom = $ci->nom;
        $c->montantInscription = $ci->montantInscription;
        $c->montantMensualite = $ci->montantMensualite;
        $c->montantAutresFrais = $ci->montantAutresFrais;
        $c->filiereId = $req->filiereId;
        $this->classes[$c->id] = $c;


        $res = new CreateClasseResponse();
        $res->classeId = $c->id;
        return $res;
    }
    public function AllClasses(AllClassesRequest $req): AllClassesResponse
    {
        $all = array_values($this->classes);
        $total = count($all);
        $page = $req->page ?? 1;
        $size = $req->pageSize ?? $total;
        $offset = max(0, ($page - 1) * $size);
        $slice = array_slice($all, $offset, $size);


        $res = new AllClassesResponse();
        $res->classes = $slice;
        $res->total = $total;
        return $res;
    }


    public function SearchClasseById(SearchClasseByIdRequest $req): SearchClasseByIdResponse
    {
        $res = new SearchClasseByIdResponse();
        $res->classes = $this->classes[$req->id] ?? null;
        return $res;
    }


    public function SearchClasseByCode(SearchClasseByCodeRequest $req): SearchClasseByCodeResponse
    {
        $found = null;
        foreach ($this->classes as $c) {
            if ($req->code !== null && $c->code === $req->code) {
                $found = $c;
                break;
            }
        }
        $res = new SearchClasseByCodeResponse();
        $res->classes = $found;
        return $res;
    }


    public function SearchClassesByName(SearchClassesByNameRequest $req): SearchClassesByNameResponse
    {
        $names = array_map('strval', $req->nom ?? []);
        $matches = array_values(array_filter($this->classes, function (Classe $c) use ($names) {
            if (!$names) return true;
            foreach ($names as $n) {
                if ($n !== '' && stripos($c->nom, $n) !== false) return true;
            }
            return false;
        }));
        $res = new SearchClassesByNameResponse();
        $res->classe = $matches;
        $res->total = count($matches);
        return $res;
    }

    public function CreateFiliereRequest($parameters)
    {
        return $this->CreateFiliere($parameters);
    }
    public function CreateClasseRequest($parameters)
    {
        return $this->CreateClasse($parameters);
    }
    public function AllClassesRequest($parameters)
    {
        return $this->AllClasses($parameters);
    }
    public function SearchClassesByNameRequest($p)
    {
        return $this->SearchClassesByName($p);
    }
    public function SearchClasseByIdRequest($p)
    {
        return $this->SearchClasseById($p);
    }
    public function SearchClasseByCodeRequest($p)
    {
        return $this->SearchClasseByCode($p);
    }
}
