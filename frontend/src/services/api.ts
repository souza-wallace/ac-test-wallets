const API_BASE_URL = 'http://localhost:8001/api';

export interface RegisterData {
  name: string;
  email: string;
  password: string;
}

export interface LoginData {
  email: string;
  password: string;
}

export interface Transaction {
  id: number;
  wallet_id: number;
  user_id: number;
  type: string;
  amount: number;
  related_wallet?: number;
  description?: string;
  reference_id?: number;
  status: string;
  created_at: string;
  can_reverse?: boolean;
}

export interface ApiResponse<T> {
  data?: T;
  message?: string;
  error?: string;
  details?: string;
  current_page?: number;
  per_page?: number;
  total?: number;
  last_page?: number;
}

export const api = {
  register: async (data: RegisterData): Promise<ApiResponse<any>> => {
    const response = await fetch(`${API_BASE_URL}/users`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
    
    return response.json();
  },

  login: async (data: LoginData): Promise<ApiResponse<{ token: string }>> => {
    const response = await fetch(`${API_BASE_URL}/login`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify(data),
    });
    
    return response.json();
  },

  getTransactions: async (page = 1, perPage = 15): Promise<ApiResponse<Transaction[]>> => {
    const token = localStorage.getItem('token');
    const response = await fetch(`${API_BASE_URL}/wallet/transactions?page=${page}&per_page=${perPage}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });
    
    return response.json();
  },

  getUserProfile: async (): Promise<ApiResponse<any>> => {
    const token = localStorage.getItem('token');
    const response = await fetch(`${API_BASE_URL}/users/1`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });
    
    return response.json();
  },

  transfer: async (toUserEmail: string, amount: number, description: string|null): Promise<ApiResponse<any>> => {
    const token = localStorage.getItem('token');
    const response = await fetch(`${API_BASE_URL}/wallet/transfer`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({
        email: toUserEmail,
        amount: amount,
        description: description
      }),
    });
    
    return response.json();
  },

  deposit: async (amount: number): Promise<ApiResponse<any>> => {
    const token = localStorage.getItem('token');
    const response = await fetch(`${API_BASE_URL}/wallet/deposit`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
      body: JSON.stringify({
        amount: amount,
      }),
    });
    
    return response.json();
  },

  reverseTransaction: async (transactionId: number): Promise<ApiResponse<any>> => {
    const token = localStorage.getItem('token');
    const response = await fetch(`${API_BASE_URL}/wallet/transactions/${transactionId}/reverse`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${token}`,
      },
    });
    
    return response.json();
  },
};