<?php


namespace App\Repositories;


use App\Interfaces\PessoaRepositoryInterface;
use App\Models\Pessoa;
use DB;
use Exception;
use Illuminate\Http\Request;
use mysqli_sql_exception;
use Validator;

class PessoaRepository implements PessoaRepositoryInterface
{

    public function Listar($id = null)
    {
        $pessoa = Pessoa::selectRaw("id, nome, cpf, dt_nascimento, email");
        $arr_retorno = ['dados' => !$id ? $pessoa->get() : $pessoa->where('id','=',$id)->first(), 'code' => 200];
        return $arr_retorno;
    }

    public function Inserir(Request $request)
    {
        DB::beginTransaction();
        try {
            $retorno_validacao = $this->Validacao($request);

            $dados = json_decode($request->getContent(), true);

            if ($retorno_validacao != '')
                throw new Exception($retorno_validacao, 422);

            if(Pessoa::where('cpf','=',$dados['cpf'])->get()->count() > 0)
                throw new Exception('Já existe uma pessoa com esse cpf cadastrado!',422);

            $pessoa = new Pessoa();
            $pessoa->nome = $dados['nome'];
            $pessoa->cpf = $dados['cpf'];
            $pessoa->dt_nascimento = $dados['dt_nascimento'];
            $pessoa->email = $dados['email'];
            $pessoa->save();

            DB::commit();
            return ['dados' => null, 'code' => 201];

        } catch (mysqli_sql_exception $sql_ex) {
            DB::rollBack();
            return ['dados' => 'Erro interno relacionado ao banco de dados: ' . $sql_ex->getCode(), 'code' => 500];

        } catch (Exception $e) {
            DB::rollBack();
            return ['dados' => $e->getMessage(), 'code' => $e->getCode()];
        }


    }

    private function Validacao(Request $request, $insert = true)
    {
        if (!$request->getContent())
            return 'Informe um JSON como paramêtro';

        $dados = json_decode($request->getContent(), true);

        $validator = Validator::make($dados, [
            'nome' => 'required',
            'cpf' => 'required|cpf_valid',
            'dt_nascimento' => 'required|date|date_format:"Y-m-d"',
            'email' => 'required|email'
        ], [
            'nome.required' => 'Informe o nome da pessoa!',
            'cpf.required' => 'Informe o CPF da pessoa!',
            'dt_nascimento.required' => 'Informe a Data de nascimento da pessoa!',
            'dt_nascimento.date' => 'Data de nascimento precisa estar o formato Y-m-d!',
            'dt_nascimento.date_format' => 'Data de nascimento precisa estar o formato Y-m-d!',
            'email.required' => 'Informe o email!',
            'email.email' => 'Informe o email corretamente!',
        ]);

        if ($validator->fails()) {
            return $validator->messages()->first();
        }

        return '';

    }

    public function Atualizar(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $retorno_validacao = $this->Validacao($request);

            $dados = json_decode($request->getContent(),true);

            if ($retorno_validacao != '')
                throw new Exception($retorno_validacao, 422);

            $pessoa = Pessoa::find($id);
            if (!$pessoa)
                throw new Exception("Pessoa com o ID informado não encontrado!", 422);

            $pessoa->nome = $dados['nome'];
            $pessoa->cpf = $dados['cpf'];
            $pessoa->dt_nascimento = $dados['dt_nascimento'];
            $pessoa->email = $dados['email'];
            $pessoa->update();

            DB::commit();
            return ['dados' => null, 'code' => 201];

        } catch (mysqli_sql_exception $sql_ex) {
            DB::rollBack();
            return ['dados' => 'Erro interno relacionado ao banco de dados: ' . $sql_ex->getCode(), 'code' => 500];

        } catch (Exception $e) {
            DB::rollBack();
            return ['dados' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }

    public function Deletar($id)
    {
        DB::beginTransaction();
        try {
            $pessoa = Pessoa::find($id);
            if (!$pessoa)
                throw new Exception("Pessoa com o ID informado não encontrado!", 422);
            $pessoa->delete();
            DB::commit();
            return ['dados' => null, 'code' => 200];

        } catch (mysqli_sql_exception $sql_ex) {
            DB::rollBack();
            return ['dados' => 'Erro interno relacionado ao banco de dados: ' . $sql_ex->getCode(), 'code' => 500];
        } catch (Exception $e) {
            DB::rollBack();
            return ['dados' => $e->getMessage(), 'code' => $e->getCode()];
        }
    }


}
