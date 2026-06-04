export interface User {
  id: number;
  name: string;
  email: string;
  role: 'candidate' | 'employer' | 'admin';
  created_at: string;
}