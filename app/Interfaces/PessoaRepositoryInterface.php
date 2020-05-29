<?php


namespace App\Interfaces;
use Illuminate\Http\Request;

interface PessoaRepositoryInterface
{
    public function Listar($id = null);

    public function Inserir(Request $request);

    public function Atualizar(Request $request, $id);

    public function Deletar($id);
}
