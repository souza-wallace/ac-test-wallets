import { useState, useEffect } from "react";
import { Link, useNavigate } from "react-router-dom";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs";
import { useToast } from "@/hooks/use-toast";
import { api } from "@/services/api";
import { ArrowLeft, Plus, Wallet, DollarSign, CreditCard, QrCode } from "lucide-react";

const Deposito = () => {
  const [amount, setAmount] = useState("");
  const [isLoading, setIsLoading] = useState(false);
  const [currentBalance, setCurrentBalance] = useState(0);
  const [loading, setLoading] = useState(true);
  const { toast } = useToast();
  const navigate = useNavigate();

  useEffect(() => {
    loadUserBalance();
  }, []);

  const loadUserBalance = async () => {
    try {
      const response = await api.getUserProfile();
      if (response.error) {
        toast({
          title: "Erro ao carregar saldo",
          description: response.error,
          variant: "destructive",
        });
      } else if (response.data) {
        setCurrentBalance(response.data.wallet?.balance || 0);
      }
    } catch (error) {
      toast({
        title: "Erro de conexão",
        description: "Não foi possível carregar o saldo",
        variant: "destructive",
      });
    } finally {
      setLoading(false);
    }
  };

  const formatCurrency = (value: number) => {
    return new Intl.NumberFormat('pt-BR', {
      style: 'currency',
      currency: 'BRL'
    }).format(value);
  };

  const handleDeposit = async (method: string) => {
    const depositAmount = parseFloat(amount);
    
    if (depositAmount <= 0) {
      toast({
        title: "Valor inválido",
        description: "O valor deve ser maior que zero.",
        variant: "destructive",
      });
      return;
    }

    setIsLoading(true);

    try {
      const response = await api.deposit(depositAmount);
      
      if (response.error) {
        toast({
          title: "Erro no depósito",
          description: response.error || response.details,
          variant: "destructive",
        });
      } else {
        toast({
          title: "Depósito realizado!",
          description: `${formatCurrency(depositAmount)} adicionado à sua conta`,
        });
        
        setAmount("");
        
        // Recarrega o saldo
        loadUserBalance();
        
        // Redireciona para dashboard após 2 segundos
        setTimeout(() => {
          navigate('/dashboard');
        }, 2000);
      }
    } catch (error) {
      toast({
        title: "Erro de conexão",
        description: "Não foi possível processar o depósito",
        variant: "destructive",
      });
    } finally {
      setIsLoading(false);
    }
  };

  const quickAmounts = [50, 100, 200, 500];

  return (
    <div className="min-h-screen bg-background">
      {/* Header */}
      <header className="border-b border-border bg-card">
        <div className="max-w-4xl mx-auto px-4 py-4 flex items-center gap-4">
          <Link to="/dashboard">
            <Button variant="outline" size="sm">
              <ArrowLeft className="w-4 h-4 mr-2" />
              Voltar
            </Button>
          </Link>
          <div className="flex items-center gap-3">
            <div className="w-10 h-10 bg-success rounded-full flex items-center justify-center">
              <Plus className="w-5 h-5 text-success-foreground" />
            </div>
            <div>
              <h1 className="font-semibold text-lg">Depositar dinheiro</h1>
              <p className="text-sm text-muted-foreground">Adicione fundos à sua carteira</p>
            </div>
          </div>
        </div>
      </header>

      <div className="max-w-2xl mx-auto px-4 py-8">
        <div className="grid gap-6">
          {/* Balance Card */}
          <Card className="bg-gradient-to-r from-success to-success-light text-success-foreground">
            <CardContent className="p-6">
              <div className="flex items-center justify-between">
                <div>
                  <p className="text-success-foreground/70 text-sm">Saldo atual</p>
                  <p className="text-2xl font-bold">
                    {loading ? "Carregando..." : formatCurrency(currentBalance)}
                  </p>
                </div>
                <Wallet className="w-8 h-8 text-success-foreground/70" />
              </div>
            </CardContent>
          </Card>

          {/* Amount Input */}
          <Card>
            <CardHeader>
              <CardTitle>Valor do depósito</CardTitle>
              <CardDescription>
                Escolha o valor que deseja depositar
              </CardDescription>
            </CardHeader>
            <CardContent className="space-y-4">
              <div className="space-y-2">
                <Label htmlFor="amount">Valor (R$)</Label>
                <div className="relative">
                  <DollarSign className="absolute left-3 top-3 h-4 w-4 text-muted-foreground" />
                  <Input
                    id="amount"
                    type="number"
                    step="0.01"
                    min="0.01"
                    placeholder="0,00"
                    value={amount}
                    onChange={(e) => setAmount(e.target.value)}
                    className="pl-10 text-lg font-semibold"
                  />
                </div>
              </div>

              {/* Quick Amount Buttons */}
              <div className="space-y-2">
                <Label>Valores rápidos</Label>
                <div className="grid grid-cols-4 gap-2">
                  {quickAmounts.map((quickAmount) => (
                    <Button
                      key={quickAmount}
                      variant="outline"
                      size="sm"
                      onClick={() => setAmount(quickAmount.toString())}
                      className="text-xs"
                    >
                      {formatCurrency(quickAmount)}
                    </Button>
                  ))}
                </div>
              </div>

              {/* Preview */}
              {amount && parseFloat(amount) > 0 && (
                <Card className="bg-success/5 border-success/20">
                  <CardContent className="p-4">
                    <h3 className="font-semibold mb-2 text-success">Após o depósito</h3>
                    <div className="space-y-1 text-sm">
                      <div className="flex justify-between">
                        <span>Saldo atual:</span>
                        <span>{formatCurrency(currentBalance)}</span>
                      </div>
                      <div className="flex justify-between">
                        <span>Valor do depósito:</span>
                        <span className="font-semibold text-success">
                          +{formatCurrency(parseFloat(amount))}
                        </span>
                      </div>
                      <hr className="my-2" />
                      <div className="flex justify-between font-semibold text-success">
                        <span>Novo saldo:</span>
                        <span>{formatCurrency(currentBalance + parseFloat(amount))}</span>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              )}
            </CardContent>
          </Card>

          {/* Payment Methods */}
          <Card>
            <CardHeader>
              <CardTitle>Método de pagamento</CardTitle>
              <CardDescription>
                Escolha como deseja fazer o depósito
              </CardDescription>
            </CardHeader>
            <CardContent>
              <Tabs defaultValue="pix" className="w-full">
                <TabsList className="grid w-full grid-cols-3">
                  <TabsTrigger value="pix">PIX</TabsTrigger>
                  <TabsTrigger value="cartao">Cartão</TabsTrigger>
                  <TabsTrigger value="transferencia">Transferência</TabsTrigger>
                </TabsList>
                
                <TabsContent value="pix" className="space-y-4">
                  <div className="text-center py-8">
                    <QrCode className="w-16 h-16 mx-auto mb-4 text-muted-foreground" />
                    <h3 className="font-semibold mb-2">Depósito via PIX</h3>
                    <p className="text-sm text-muted-foreground mb-4">
                      Instantâneo e sem taxa
                    </p>
                    <Button 
                      onClick={() => handleDeposit("PIX")}
                      disabled={!amount || parseFloat(amount) <= 0 || isLoading}
                      className="w-full"
                    >
                      {isLoading ? "Processando..." : "Depositar via PIX"}
                    </Button>
                  </div>
                </TabsContent>
                
                <TabsContent value="cartao" className="space-y-4">
                  <div className="text-center py-8">
                    <CreditCard className="w-16 h-16 mx-auto mb-4 text-muted-foreground" />
                    <h3 className="font-semibold mb-2">Cartão de Crédito/Débito</h3>
                    <p className="text-sm text-muted-foreground mb-4">
                      Processamento em até 2 dias úteis
                    </p>
                    <Button 
                      onClick={() => handleDeposit("Cartão")}
                      disabled={!amount || parseFloat(amount) <= 0 || isLoading}
                      className="w-full"
                    >
                      {isLoading ? "Processando..." : "Depositar via Cartão"}
                    </Button>
                  </div>
                </TabsContent>
                
                <TabsContent value="transferencia" className="space-y-4">
                  <div className="text-center py-8">
                    <Wallet className="w-16 h-16 mx-auto mb-4 text-muted-foreground" />
                    <h3 className="font-semibold mb-2">Transferência Bancária</h3>
                    <p className="text-sm text-muted-foreground mb-4">
                      TED/DOC - Processamento em 1 dia útil
                    </p>
                    <Button 
                      onClick={() => handleDeposit("Transferência Bancária")}
                      disabled={!amount || parseFloat(amount) <= 0 || isLoading}
                      className="w-full"
                    >
                      {isLoading ? "Processando..." : "Depositar via Transferência"}
                    </Button>
                  </div>
                </TabsContent>
              </Tabs>
            </CardContent>
          </Card>
        </div>
      </div>
    </div>
  );
};

export default Deposito;