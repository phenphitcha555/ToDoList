<?php
	use \Psr\Http\Message\ServerRequestInterface as Request;
	use \Psr\Http\Message\ResponseInterface as Response;

	$app = new \Slim\App;

	$app->options('/{routes:.+}', function ($request, $response, $args) {
		return $response;
	});

	$app->add(function ($req, $res, $next) {
		$response = $next($req, $res);
		return $response
				->withHeader('Access-Control-Allow-Origin', '*')
				->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
				->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	});

	// Get all tasks
	$app->get('/task/all', function(Request $request, Response $response){
		$sql = "SELECT * FROM tasklist";

		try{
			// Get DB Object
			$db = new db();
			// Connect
			$db = $db->connect();

			$stmt = $db->query($sql);
			$tasks = $stmt->fetchAll(PDO::FETCH_OBJ);
			$db = null;
			echo json_encode($tasks);
		} catch(PDOException $e){
			echo '{"error": {"text": '.$e->getMessage().'}}';
		}
	});

	// Get single task by id
	$app->get('/task/{id}', function(Request $request, Response $response){
		$id = $request->getAttribute('id');

		$sql = "SELECT * FROM tasklist WHERE id = $id";

		try{
			// Get DB Object
			$db = new db();
			
			// Connect
			$db = $db->connect();
			$stmt = $db->query($sql);
			$task = $stmt->fetch(PDO::FETCH_OBJ);
			$db = null;
			echo json_encode($task);
		} catch(PDOException $e){
			echo '{"error": {"text": '.$e->getMessage().'}}';
		}
	});

	// Add new task
	$app->post('/task/add', function(Request $request, Response $response){
		$task_name = $request->getParam('task_name');
		$task_detail = $request->getParam('task_detail');
		$status = $request->getParam('status');

		$sql = "INSERT INTO tasklist(task_name,task_detail,status) VALUES(:task_name,:task_detail,:status)";

		try{
			// Get DB Object
			$db = new db();
			
			// Connect
			$db = $db->connect();
			$stmt = $db->prepare($sql);

			$stmt->bindParam(':task_name',		$task_name);
			$stmt->bindParam(':task_detail',	$task_detail);
			$stmt->bindParam(':status',			$status);

			$stmt->execute();

			echo '{"notice": {"text": "New Task Added"}}';
		} catch(PDOException $e){
			echo '{"error": {"text": '.$e->getMessage().'}}';
		}
	});

	// Edit task by id
	$app->put('/task/edit/{id}', function(Request $request, Response $response){
		$id = $request->getAttribute('id');
		$task_name = $request->getParam('task_name');
		$task_detail = $request->getParam('task_detail');
		$status = $request->getParam('status');

		$sql = "UPDATE tasklist SET
					task_name 	= :task_name,
					task_detail	= :task_detail,
					status		= :status
				WHERE id = $id";

		try{
			// Get DB Object
			$db = new db();
			
			// Connect
			$db = $db->connect();
			$stmt = $db->prepare($sql);

			$stmt->bindParam(':task_name',		$task_name);
			$stmt->bindParam(':task_detail',	$task_detail);
			$stmt->bindParam(':status',			$status);

			$stmt->execute();

			echo '{"notice": {"text": "Task Updated"}}';

		} catch(PDOException $e){
			echo '{"error": {"text": '.$e->getMessage().'}}';
		}
	});

	// Delete task by id
	$app->delete('/task/delete/{id}', function(Request $request, Response $response){
		$id = $request->getAttribute('id');

		$sql = "DELETE FROM tasklist WHERE id = $id";

		try{
			// Get DB Object
			$db = new db();
			
			// Connect
			$db = $db->connect();
			$stmt = $db->prepare($sql);
			$stmt->execute();
			$db = null;
			echo '{"notice": {"text": "Task Deleted"}}';
		} catch(PDOException $e){
			echo '{"error": {"text": '.$e->getMessage().'}}';
		}
	});

	// Update task status by id
	$app->put('/task/update_status/{id}', function(Request $request, Response $response){
		$id = $request->getAttribute('id');
		$status = $request->getParam('status');

		$sql = "UPDATE tasklist SET
					status	= :status
				WHERE id = $id";

		try{
			// Get DB Object
			$db = new db();
			
			// Connect
			$db = $db->connect();
			$stmt = $db->prepare($sql);

			$stmt->bindParam(':status',	$status);

			$stmt->execute();

			echo '{"notice": {"text": "Task Status Updated"}}';

		} catch(PDOException $e){
			echo '{"error": {"text": '.$e->getMessage().'}}';
		}
	});
?>