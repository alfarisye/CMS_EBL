<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Webhook extends BaseController
{
    public function index()
    {
        $payload = file_get_contents('php://input');
        $data = json_decode($payload, true);

        if (isset($data['ref']) && isset($data['commit_sha'])) {
            $ref = $data['ref'];
            $commit_sha = $data['commit_sha'];
        
            // Log the received data
            file_put_contents(WRITEPATH . 'uploads/webhook.log', "Received ref: $ref, commit_sha: $commit_sha\n", FILE_APPEND);
        
            // Pull the changes from the repository
            $repo_path = '/var/www/html/cms-ebl';
            chdir($repo_path); // Change the current working directory
            $git_pull_output = shell_exec('git pull 2>&1');
            file_put_contents(WRITEPATH . 'uploads/webhook.log', "Git pull output: $git_pull_output\n", FILE_APPEND);

            // Send a response
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Webhook received and processed']);
        } else {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid webhook payload']);
        }
    }
}
