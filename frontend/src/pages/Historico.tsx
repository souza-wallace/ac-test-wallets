import { useState, useEffect } from "react";
import { Link } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { useToast } from "@/hooks/use-toast";
import { api, Transaction } from "../services/api";
import { 
  ArrowLeft, 
  History, 
  Plus, 
  ArrowUpRight, 
  ArrowDownLeft,
  RotateCcw
} from "lucide-react";

const Historico = () => {
  const [transactions, setTransactions] = useState<Transaction[]>([]);
  const [loading, setLoading] = useState(true);
  const [User, setUser] = useState<any>(null);
  const { toast } = useToast();

  useEffect(() => {
    loadUserData();
    loadTransactions();
  }, []);

  const loadTransactions = async () => {
    try {
      setLoading(true);
      const response = await api.getTransactions(1, 50);
      
      if (response.error) {
        toast({
          title: "Erro ao carregar transações",
          description: response.error,
          variant: "destructive",
        });
      } else {
        setTransactions(response.data || []);
      }
    } catch (error) {
      toast({
        title: "Erro de conexão",
        description: "Não foi possível carregar as transações",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const loadUserData = async () => {
    try {
      const response = await api.getUserProfile();
      if (response.error) {
        toast({
          title: "Erro ao carregar dados",
          description: response.error,
          variant: "destructive",
        });
      } else if (response.data) {
        console.log(response.data)
        setUser(response.data);
      }
    } catch (error) {
      toast({
        title: "Erro de conexão",
        description: "Não foi possível carregar os dados do usuário",
        variant: "destructive",
      });
    }
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(value);
  };

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('pt-BR', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    });
  };

  const getTransactionIcon = (type: string) => {
    switch (type.toLowerCase()) {
      case "deposit":
        return <Plus className="w-4 h-4 text-green-600" />;
      case "transfer":
        return <ArrowUpRight className="w-4 h-4 text-red-600" />;
      case "receive":
        return <ArrowDownLeft className="w-4 h-4 text-green-600" />;
      default:
        return <History className="w-4 h-4" />;
    }
  };

  const getStatusBadge = (status: string) => {
    switch (status.toLowerCase()) {
      case 'completed':
        return <Badge variant="default">Concluída</Badge>;
      case 'pending':
        return <Badge variant="secondary">Pendente</Badge>;
      case 'reversed':
        return <Badge variant="destructive">Revertida</Badge>;
      default:
        return <Badge variant="outline">Desconhecido</Badge>;
    }
  };

  const handleReverseTransaction = async (transactionId: number) => {
    try {
      const response = await api.reverseTransaction(transactionId);
      
      if (response.error) {
        toast({
          title: "Erro ao reverter",
          description: response.error || response.details,
          variant: "destructive",
        });
      } else {
        toast({
          title: "Transação revertida",
          description: "A transação foi revertida com sucesso.",
        });
        
        // Recarrega as transações
        loadTransactions();
      }
    } catch (error) {
      toast({
        title: "Erro de conexão",
        description: "Não foi possível reverter a transação",
        variant: "destructive",
      });
    }
  };

  return (
    <div className="min-h-screen bg-background p-4">
      <div className="max-w-2xl mx-auto">
        <div className="mb-4">
          <Link to="/dashboard">
            <Button variant="outline" size="sm">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Voltar
            </Button>
          </Link>
        </div>

        <Card>
          <CardHeader>
            <CardTitle className="flex items-center gap-2">
              <History className="w-5 h-5" />
              Histórico de Transações
            </CardTitle>
            <CardDescription>
              {transactions.length} transações encontradas
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div className="space-y-4">
              {loading ? (
                <div className="text-center py-8">
                  <p className="text-muted-foreground">Carregando transações...</p>
                </div>
              ) : transactions.length === 0 ? (
                <div className="text-center py-8">
                  <History className="w-12 h-12 mx-auto mb-4 text-muted-foreground" />
                  <p className="text-muted-foreground">Nenhuma transação encontrada</p>
                </div>
              ) : (
                transactions.map((transaction) => {
                  const isSent = transaction.type === "TRANSFER" && User && transaction.user_id === User.id;
                  const isReceived = transaction.type === "TRANSFER" && User && transaction.user_id !== User.id;
                
                  return (
                    <div
                      key={transaction.id}
                      className="flex items-center justify-between p-4 rounded-lg border"
                    >
                      <div className="flex items-center gap-4">
                        <div className="w-10 h-10 bg-muted rounded-full flex items-center justify-center">
                          {getTransactionIcon(transaction.type)}aaa
                        </div>
                        <div className="space-y-1">
                          <p className="font-medium">
                            {transaction.description || "Transação"}
                          </p>
                
                          <p className="font-medium">
                            {isSent && `To: ${transaction?.recipient_wallet?.user.name}`}
                            {isReceived && `From: ${transaction?.user?.name}`}
                          </p>
                
                          <p className="text-sm text-muted-foreground">
                            {formatDate(transaction.created_at)}
                          </p>
                        </div>
                      </div>
                
                      <div className="text-right space-y-2">
                        <div className="space-y-1">
                          <p className="font-semibold">{formatCurrency(transaction.amount)}</p>
                          {getStatusBadge(transaction.status)}
                        </div>
                
                        {transaction.can_reverse && transaction.status === "COMPLETED" && (
                          <Button
                            variant="outline"
                            size="sm"
                            onClick={() => handleReverseTransaction(transaction.id)}
                            className="text-xs"
                          >
                            <RotateCcw className="w-3 h-3 mr-1" />
                            Reverter
                          </Button>
                        )}
                
                        {!transaction.can_reverse && transaction.status === "COMPLETED" && (
                          <Badge variant="outline">Revertida</Badge>
                        )}
                      </div>
                    </div>
                  );
                })
                
              )}
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  );
};

export default Historico;