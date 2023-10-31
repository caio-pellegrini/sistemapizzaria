<?php
include_once("conn.php");

$method = $_SERVER["REQUEST_METHOD"];

// Resgate dos dados, montagem do pedido
if ($method === "GET") {
    
    $bordasQuery = $conn->query("SELECT * FROM borda");
    $bordas = $bordasQuery->fetchAll();

    $massasQuery = $conn->query("SELECT * FROM massa");
    $massas = $massasQuery->fetchAll();

    $saboresQuery = $conn->query("SELECT * FROM sabor");
    $sabores = $saboresQuery->fetchAll();

    //print_r($sabores); exit;

// Criação do pedido

} elseif ($method == "POST") {
    // Lógica de processamento para POST

    $data = $_POST;

    $borda = $data["borda"];
    $massa = $data["massa"];
    $sabores = $data["sabores"];

    // validação de sabores máximos

    if(count($sabores) > 3) {
    /* Irá inserir uma msg na sessão e retornará um aviso Warning e retornará à página inicial */

    $_SESSION["msg"] = "Selecione no máximo 3 sabores!";
    $_SESSION["status"] = "warning";

    } else {
        // salvando a borda e massa na pizza

        // prepare o stmt (statement) instrução preparada - filtra os dados selecionados pelo usuario
        $stmt = $conn->prepare("INSERT INTO pizza (borda_id, massa_id) VALUES (:borda, :massa)"); // Os : para filtrar

        // filtrando inputs - O bindParam liga o valor a uma variável

        $stmt->bindParam(":borda", $borda, PDO::PARAM_INT); // o PDO- será o filtro que é uma validação de inteiro
        $stmt->bindParam(":massa", $massa, PDO::PARAM_INT);

        $stmt->execute();

        // resgatando último id da última pizza
        $pizzaId = $conn->lastInsertId();

        $stmt = $conn->prepare("INSERT INTO pizza_sabor (pizza_id, sabor_id) VALUES (:pizza, :sabor)");

        // repetição até terminar de salvar todos os sabores

        foreach($sabores as $sabor) {

            // filtrandos inputs
            $stmt->bindParam(":pizza", $pizzaId, PDO::PARAM_INT);
            $stmt->bindParam(":sabor", $sabor, PDO::PARAM_INT);

            $stmt->execute();
        }

        // criar o pedido da pizza
        $stmt=$conn->prepare("INSERT INTO pedido (pizza_id, status_id) VALUES (:pizza, :estatus)");

        // status -> sempre inicia com 1, que é em produção
        $statusId= 1;

        // filtrar inputs
        $stmt->bindParam(":pizza", $pizzaId);
        $stmt->bindParam(":estatus", $statusId);

        $stmt->execute();

        // Exibir mensagem de sucesso
        $_SESSION["msg"] = "Pedido realizado com sucesso";
        $_SESSION["status"] = "success";
    }

    // Retornará para página principal
    header("Location: ..");

    



}

?>
