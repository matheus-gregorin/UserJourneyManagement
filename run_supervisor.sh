# Buscando status do supervisor
systemctl status supervisor

# Habilitando ele
systemctl enable supervisor

# reiniciando
systemctl restart supervisor

systemctl status supervisor

# Verificando se na porta 9001 não tem nada rodando, se sim, matamos o serviço
for PORT in 9001; do
  PID=$(lsof -ti :$PORT)
  if [ -n "$PID" ]; then
    echo "Matando processos na porta $PORT (PID: $PID)..."
    kill -9 $PID
  else
    echo "Nenhum processo encontrado na porta $PORT."
  fi
done

# Iniciamos uma releitura e update do arquivo
supervisorctl reread
supervisorctl update

# reiniciamos o serviço do supervisor
systemctl restart supervisor

# Iniciar o servidor Laravel
php artisan serve --host=0.0.0.0 --port=8000