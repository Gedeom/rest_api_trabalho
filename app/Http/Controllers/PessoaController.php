<?php

namespace App\Http\Controllers;

use App\Interfaces\PessoaRepositoryInterface;
use App\Models\Pessoa;
use Illuminate\Http\Request;

class PessoaController extends Controller
{
    /**
     * @OA\Info(
     *     description="This is a sample Pessoas server.  You can find
    out more about Swagger at
    [http://swagger.io](http://swagger.io) or on
    [irc.freenode.net, #swagger](http://swagger.io/irc/).",
     *     version="1.0.0",
     *     title="Swagger Petstore",
     *     termsOfService="http://swagger.io/terms/",
     *     @OA\Contact(
     *         email="apiteam@swagger.io"
     *     ),
     *     @OA\License(
     *         name="Apache 2.0",
     *         url="http://www.apache.org/licenses/LICENSE-2.0.html"
     *     )
     * )
     */

    private $pessoa;

    //aqui eu passo somente a interface, mas o laravel avisa o controller que tem uma classe implementando essa interface, ai ja pega os metodos delas para essa variavel, la em AppServiceProvider
    public function __construct(PessoaRepositoryInterface $pessoa)
    {
        $this->pessoa = $pessoa;
    }

    /**
     * @OA\Get(
     *     tags={"pessoa"},
     *     summary="Retornar lista de Pessoas",
     *     description="Retornar objeto de pessoas",
     *     path="/pessoas",
     *     @OA\Response(response="200", description="Lista de Pessoas"),
     * ),
     *
     */
    public function Get($id = null)
    {
        $retorno = $this->pessoa->Listar($id);

        return response()->json($retorno['dados'], $retorno['code'],[],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\PUT(
     *     tags={"pessoa"},
     *     summary="Atualizar uma pessoa",
     *     description="Atualizar uma pessoa",
     *     path="/pessoas/id",
     *     @OA\Response(response="201", description="Atualizar Pessoa"),
     * ),
     *
     */

    public function Update(Request $request, $id)
    {
        $retorno = $this->pessoa->Atualizar($request, $id);

        return response()->json($retorno['dados'], $retorno['code'],[],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\POST(
     *     tags={"pessoa"},
     *     summary="Inserção de uma pessoa",
     *     description="Inserção uma pessoa",
     *     path="/pessoas",
     *     @OA\Response(response="201", description="Inserção de uma pessoa"),
     * ),
     *
     */

    public function Create(Request $request)
    {
        $retorno = $this->pessoa->Inserir($request);

        return response()->json($retorno['dados'], $retorno['code'],[],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)->header('Content-Type', 'application/json');
    }

    /**
     * @OA\POST(
     *     tags={"pessoa"},
     *     summary="Deletar uma pessoa",
     *     description="Deletar uma pessoa",
     *     path="/pessoas/id",
     *     @OA\Response(response="200", description="Deletar uma pessoa"),
     * ),
     *
     */

    public function Delete($id)
    {
        $retorno = $this->pessoa->Deletar($id);

        return response()->json($retorno['dados'], $retorno['code'],[],JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)->header('Content-Type', 'application/json');
    }

}
