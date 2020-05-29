<?php


namespace App\Repositories;


use App\Interfaces\PessoaRepositoryInterface;
use App\Models\Pessoa;
use DB;
use Exception;
use Illuminate\Http\Request;
use mysqli_sql_exception;
use Validator;

//aqui é onde é implementada a interface
class PessoaRepository implements PessoaRepositoryInterface
{

    //aqui é o metodo de lista, do verbo Get,se a pessoa passar o id na rota, então sera retornado um objeto, senão um array com todas pessoas
    public function Listar($id = null)
    {
        //aqui o select normal com os campos
        $pessoa = Pessoa::selectRaw("id, nome, cpf, dt_nascimento, email");
        /*aqui é o retorno para o controller sempre vai ser um array com dois indexes, dados e code, que vai ser a mensagem se der erro ou os dados se foi tudo certo,
        juntamente com o codigo
        */
        $arr_retorno = ['dados' => !$id ? $pessoa->get() : $pessoa->where('id', '=', $id)->first(), 'code' => 200];
        return $arr_retorno;
    }

    //metodo de inserção, Verbo POST
    public function Inserir(Request $request)
    {


        //criação de transação no Banco de dados, caso der errado, voltar o que era antes
        DB::beginTransaction();
        try {
            //funcao de validação dos dados que vem para o servidor, somente sera usado nos VERBOS POST E PUST (adicionar e atualizar), se faltar coluna ou outra validação der errado retorna erro
            $retorno_validacao = $this->Validacao($request);

            $dados = json_decode($request->getContent(), true);

            //se falhar a validação retorna a mensagem com o codigo 422, que é para erro de validação, na verdade vai lá pra baixo, para excessões
            if ($retorno_validacao != '')
                throw new Exception($retorno_validacao, 422);

            //verifica se existe pessoa com mesmo cpf, para evitar erro de sql chave unica
            if (Pessoa::where('cpf', '=', $dados['cpf'])->get()->count() > 0)
                throw new Exception('Já existe uma pessoa com esse cpf cadastrado!', 422);

            //instancia nova pessoa e inseri os dados e salva
            $pessoa = new Pessoa();
            $pessoa->nome = $dados['nome'];
            $pessoa->cpf = $dados['cpf'];
            $pessoa->dt_nascimento = $dados['dt_nascimento'];
            $pessoa->email = $dados['email'];
            $pessoa->save();

            //salva as alterações
            DB::commit();

            //retorna dados como null, pq é inserção, e codigo 201 pq foi criado com sucesso
            return ['dados' => null, 'code' => 201];

        } catch (mysqli_sql_exception $sql_ex) {
            //se for erro de sql, retorna somente o codigo do erro do sql, e codigo http 500, erro interno servidor
            DB::rollBack();
            return ['dados' => 'Erro interno relacionado ao banco de dados: ' . $sql_ex->getCode(), 'code' => 500];

        } catch (Exception $e) {
            //volta o banco para o estado de antes do erro
            DB::rollBack();
            //retorna o array com as instruçoes de mensagem e codigo
            return ['dados' => $e->getMessage(), 'code' => $e->getCode()];
        }


    }

    private function Validacao(Request $request, $insert = true)
    {
        //caso n passe um json
        if (!$request->getContent() || $request->all() == '')
            return 'Informe um JSON como paramêtro';

        //converter json para array, pq php n trabalha com json
        $dados = json_decode($request->getContent(), true);

        $dados = !is_array($dados) ? [] : $dados;
        
        //aqui é feita as validação com a ferramenta do laravel, Validator
        $validator = Validator::make($dados, [
            'nome' => 'required',//required = requerido kkkk
            'cpf' => 'required|cpf_valid',//cpf_valid foi uma regra que criei a parte, para validar cpf, lá em CustomValidator
            'dt_nascimento' => 'required|date|date_format:"Y-m-d"', //valida a data de nascimento, com formato 1999-10-03
            'email' => 'required|email'//valida o email
        ], [//aqui são as mensagens caso der erro
            'nome.required' => 'Informe o nome da pessoa!',//regra "required" para o nome
            'cpf.required' => 'Informe o CPF da pessoa!',//required tambem
            'dt_nascimento.required' => 'Informe a Data de nascimento da pessoa!',//tambem
            'dt_nascimento.date' => 'Data de nascimento precisa estar o formato Y-m-d!',//regra "date" para data nascimento
            'dt_nascimento.date_format' => 'Data de nascimento precisa estar o formato Y-m-d!',//regra "format"
            'email.required' => 'Informe o email!',
            'email.email' => 'Informe o email corretamente!',
        ]);

        //se falhar pega a primeira mensagem e retorna
        if ($validator->fails()) {
            return $validator->messages()->first();
        }

        //padrão retorna aspas simples
        return '';

    }

    //atualizar basicamente mesma coisa da inserção
    //atualizar, Verbo PUT
    public function Atualizar(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $retorno_validacao = $this->Validacao($request);

            $dados = json_decode($request->getContent(), true);

            if ($retorno_validacao != '')
                throw new Exception($retorno_validacao, 422);

            $pessoa = Pessoa::find($id);
            //se o id que foi passado na rota não tiver no banco de dados, dá erro
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

    //Deletar, Verbo DELETE
    public function Deletar($id)
    {
        DB::beginTransaction();
        try {
            $pessoa = Pessoa::find($id);
            //se n encontrou o id para apagar da erro
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
//cabou, outra coisa que vc tem que falar é que foi vinculado a o repositorio com a interface no container do laravel, para que o controller soubesse onde encontrar a classe que estava implementando a interface


}
