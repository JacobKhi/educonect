<?php

$senha_para_o_admin = 'jacob123';

$hash_da_senha = password_hash($senha_para_o_admin, PASSWORD_DEFAULT);

echo "<h3>Senha Criptografada para o Admin</h3>";
echo "<p>Use esta senha criptografada no seu comando INSERT no próximo passo.</p>";
echo "<textarea rows='4' cols='80' readonly>" . htmlspecialchars($hash_da_senha) . "</textarea>";

?>
